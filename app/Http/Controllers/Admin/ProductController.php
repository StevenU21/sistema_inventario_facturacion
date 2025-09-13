<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UnitMeasure;
use App\Models\Warehouse;
use App\Models\Color;
use App\Models\Size;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\FileService;
use App\Services\ModelSearchService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize("viewAny", Product::class);
        $products = Product::with(['brand.category', 'tax', 'unitMeasure', 'entity'])
            ->where('status', 'available')
            ->latest()
            ->paginate(15);

        // Solo consulta catálogos una vez
        $brands = Brand::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brandsByCategory = Brand::with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($grp) => $grp->pluck('name', 'id'))
            ->toArray();
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(fn($entity) => $entity->first_name . ' ' . $entity->last_name, 'id');

        return view('admin.products.index', compact('products', 'brands', 'categories', 'units', 'taxes', 'entities', 'brandsByCategory'));
    }

    public function search(Request $request, ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Product::class);
        $params = $request->all();
        // Sanitizar ordenamientos permitidos para evitar errores
        $allowedSorts = ['id', 'name', 'brand_id', 'tax_id', 'unit_measure_id', 'entity_id', 'status', 'created_at'];
        if (!empty($params['sort']) && !in_array($params['sort'], $allowedSorts)) {
            unset($params['sort']); // Dejar que el servicio use el default
        }

        $products = $searchService->search(
            Product::class,
            $params,
            // Campos de búsqueda (incluye relación)
            ['name', 'description', 'barcode', 'brand.name'],
            // Relaciones a cargar
            ['brand.category', 'tax', 'unitMeasure', 'entity'],
            // Filtros personalizados
            function ($query, $p) {
                if (!empty($p['brand_id'])) {
                    $query->where('brand_id', $p['brand_id']);
                }
                if (!empty($p['category_id'])) {
                    $query->whereHas('brand', function ($b) use ($p) {
                        $b->where('category_id', $p['category_id']);
                    });
                }
                if (!empty($p['unit_measure_id'])) {
                    $query->where('unit_measure_id', $p['unit_measure_id']);
                }
                if (!empty($p['tax_id'])) {
                    $query->where('tax_id', $p['tax_id']);
                }
                if (!empty($p['entity_id'])) {
                    $query->where('entity_id', $p['entity_id']);
                }
                if (!empty($p['status'])) {
                    $query->where('status', $p['status']);
                }
            }
        );
        $brands = Brand::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brandsByCategory = Brand::with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($grp) => $grp->pluck('name', 'id'))
            ->toArray();
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(function ($entity) {
                return $entity->first_name . ' ' . $entity->last_name;
            }, 'id');
        return view('admin.products.index', compact('products', 'brands', 'categories', 'units', 'taxes', 'entities', 'brandsByCategory'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $brandId = $request->input('brand_id');
        $categoryId = $request->input('category_id');
        $unitId = $request->input('unit_measure_id');
        $taxId = $request->input('tax_id');
        $entityId = $request->input('entity_id');
        $status = $request->input('status');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Product::with(['brand.category', 'tax', 'unitMeasure', 'entity']);
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }
        if (!empty($categoryId)) {
            $query->whereHas('brand', function ($b) use ($categoryId) {
                $b->where('category_id', $categoryId);
            });
        }
        if (!empty($unitId)) {
            $query->where('unit_measure_id', $unitId);
        }
        if (!empty($taxId)) {
            $query->where('tax_id', $taxId);
        }
        if (!empty($entityId)) {
            $query->where('entity_id', $entityId);
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%$search%")
                        ;
                    });
            });
        }
        $allowedSorts = ['id', 'name', 'brand_id', 'tax_id', 'unit_measure_id', 'entity_id', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $timestamp = now()->format('Ymd_His');
        $filename = "productos_filtrados_{$timestamp}.xlsx";
        return Excel::download(new ProductsExport($query), $filename);
    }

    public function create()
    {
        $this->authorize("create", Product::class);
        $product = new Product();
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $brandsByCategory = Brand::with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($grp) => $grp->pluck('name', 'id'))
            ->toArray();
        $units = UnitMeasure::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(function ($entity) {
                return $entity->first_name . ' ' . $entity->last_name;
            }, 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.products.create', compact('product', 'categories', 'brands', 'units', 'entities', 'taxes', 'brandsByCategory', 'colors', 'sizes', 'warehouses'));
    }

    public function store(ProductRequest $request, FileService $fileService)
    {
        $this->authorize("create", Product::class);
        $data = $request->validated();
        DB::transaction(function () use ($request, $fileService, $data) {
            $product = new Product($data);
            // Derivar category_id desde la marca seleccionada para cumplir NOT NULL en DB
            $brand = Brand::find($data['brand_id'] ?? null);
            if ($brand) {
                $product->category_id = $brand->category_id;
            }
            if ($request->hasFile('image')) {
                $product->image = $fileService->storeLocal($product, $request->file('image'));
            }
            $product->save();

            $warehouseId = (int) ($data['warehouse_id'] ?? 0);
            $userId = optional($request->user())->getAuthIdentifier();
            if (!empty($data['details']) && $warehouseId) {
                foreach ($data['details'] as $row) {
                    $variant = ProductVariant::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'color_id' => $row['color_id'] ?? null,
                            'size_id' => $row['size_id'] ?? null,
                        ],
                        [
                            'sku' => $row['sku'] ?? null,
                            'code' => $row['code'] ?? null,
                        ]
                    );
                    $variant->fill([
                        'sku' => $row['sku'] ?? $variant->sku,
                        'code' => $row['code'] ?? $variant->code,
                    ]);
                    $variant->save();

                    $quantity = (int) ($row['quantity'] ?? 0);
                    $purchasePrice = (float) ($row['unit_price'] ?? 0);
                    $salePrice = (float) ($row['sale_price'] ?? 0);
                    $minStock = (int) ($row['min_stock'] ?? 0);

                    $inventory = Inventory::firstOrNew([
                        'product_variant_id' => $variant->id,
                        'warehouse_id' => $warehouseId,
                    ]);
                    $inventory->stock = ($inventory->exists ? $inventory->stock : 0) + $quantity;
                    $inventory->purchase_price = $purchasePrice;
                    $inventory->sale_price = $salePrice;
                    $inventory->min_stock = $minStock;
                    $inventory->save();

                    InventoryMovement::create([
                        'inventory_id' => $inventory->id,
                        'type' => 'in',
                        'quantity' => $quantity,
                        'unit_price' => $purchasePrice,
                        'total_price' => $purchasePrice * $quantity,
                        'reference' => 'Alta de producto',
                        'notes' => 'Ingreso inicial al crear producto',
                        'user_id' => $userId,
                    ]);
                }
            } else {
                $variant = new ProductVariant([
                    'product_id' => $product->id,
                    'sku' => $data['sku'] ?? null,
                    'code' => $data['code'] ?? null,
                    'color_id' => null,
                    'size_id' => null,
                ]);
                $variant->save();
            }
        });
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product)
    {
        $this->authorize("view", $product);
        $product->load(['brand.category', 'tax', 'unitMeasure', 'entity']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize("update", $product);
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $brandsByCategory = Brand::with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($grp) => $grp->pluck('name', 'id'))
            ->toArray();
        $units = UnitMeasure::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(function ($entity) {
                return $entity->first_name . ' ' . $entity->last_name;
            }, 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        // Prefill de variantes existentes del producto (solo color/talla)
        $prefillDetails = $product->variants()->get()->map(function ($v) {
            return [
                'color_id' => $v->color_id,
                'size_id' => $v->size_id,
                // Otros campos (cantidad/precios) se dejan vacíos en edición de producto
            ];
        })->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'units', 'entities', 'taxes', 'brandsByCategory', 'colors', 'sizes', 'warehouses', 'prefillDetails'));
    }

    public function update(ProductRequest $request, Product $product, FileService $fileService)
    {
        $this->authorize("update", $product);
        $data = $request->validated();
        $imagePath = $fileService->updateLocal($product, 'image', $request);
        if ($imagePath) {
            $data['image'] = $imagePath;
        }
        // Rellenar y sincronizar category_id con la marca seleccionada
        $product->fill($data);
        $brand = Brand::find($product->brand_id);
        if ($brand) {
            $product->category_id = $brand->category_id;
        }
        $product->save();
        return redirect()->route('products.index')->with('updated', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $this->authorize("destroy", $product);

        if ($product->status === 'discontinued') {
            $product->status = 'available';
            $product->save();
            return redirect()->route('products.index')->with('deleted', 'Producto rehabilitado correctamente.');
        } else {
            $product->status = 'discontinued';
            $product->save();
            return redirect()->route('products.index')->with('deleted', 'Producto descontinuado correctamente.');
        }
    }

    // Endpoint para autocompletar productos por nombre
    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        $q = Product::query();
        if ($term !== '') {
            $tokens = array_values(array_filter(preg_split('/\s+/', $term)));
            $driver = DB::getDriverName();
            $collation = 'utf8mb4_unicode_ci';
            $q->where(function ($qb) use ($tokens, $driver, $collation) {
                foreach ($tokens as $token) {
                    $like = "%$token%";
                    $qb->where(function ($sub) use ($like, $driver, $collation) {
                        if ($driver === 'mysql') {
                            $sub->whereRaw("name COLLATE $collation LIKE ?", [$like]);
                        } else {
                            $sub->where('name', 'like', $like);
                        }
                    });
                }
            });
        }

        // Solo listar productos disponibles
        $q->where('status', 'available');

        $products = $q->select(['id', 'name'])
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $suggestions = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->name,
            ];
        });

        return response()->json([
            'data' => $suggestions,
        ]);
    }
}
