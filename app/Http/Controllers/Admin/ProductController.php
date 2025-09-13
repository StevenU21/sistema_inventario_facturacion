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
        return view('admin.products.create', compact('product', 'categories', 'brands', 'units', 'entities', 'taxes', 'brandsByCategory'));
    }

    public function store(ProductRequest $request, FileService $fileService)
    {
        $this->authorize("create", Product::class);
        $data = $request->validated();
        $product = new Product($data);
        if ($request->hasFile('image')) {
            $product->image = $fileService->storeLocal($product, $request->file('image'));
        }
        $product->save();
        // Crear variante simple sin color ni talla
        $variant = new ProductVariant([
            'product_id' => $product->id,
            'color_id' => null,
            'size_id' => null,
        ]);
        $variant->save();
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
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'units', 'entities', 'taxes', 'brandsByCategory'));
    }

    public function update(ProductRequest $request, Product $product, FileService $fileService)
    {
        $this->authorize("update", $product);
        $data = $request->validated();
        $imagePath = $fileService->updateLocal($product, 'image', $request);
        if ($imagePath) {
            $data['image'] = $imagePath;
        }
        $product->update($data);
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
