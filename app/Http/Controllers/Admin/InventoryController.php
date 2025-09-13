<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Entity;
use App\Classes\InventoryMovementManager;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoriesExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Inventory::class);
        $perPage = request('per_page', 10);
        $inventories = Inventory::with([
            'productVariant.product.tax',
            'productVariant.product.unitMeasure',
            'productVariant.product.brand',
            'productVariant.product.category',
            'warehouse'
        ])->latest()->paginate($perPage);
        // Catálogo de productos para usar sus nombres sin recargar relaciones en variantes
        $products = Product::where('status', 'available')->pluck('name', 'id');
        // Evitar eager load duplicado en variantes: no cargamos relaciones; resolveremos nombres vía catálogos
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })->get();
        $colorIds = $variants->pluck('color_id')->filter()->unique()->values();
        $sizeIds = $variants->pluck('size_id')->filter()->unique()->values();
        $colors = Color::whereIn('id', $colorIds)->pluck('name', 'id');
        $sizes = Size::whereIn('id', $sizeIds)->pluck('name', 'id');
        $variantsByProduct = $variants->groupBy('product_id')->map(function ($variants) use ($products, $colors, $sizes) {
            return $variants->map(function ($variant) use ($products, $colors, $sizes) {
                // Usar nombre de producto desde el catálogo
                $label = $products->get($variant->product_id, (string) $variant->product_id);
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color_id)
                    $label .= ' / ' . ($colors[$variant->color_id] ?? $variant->color_id);
                if ($variant->size_id)
                    $label .= ' / ' . ($sizes[$variant->size_id] ?? $variant->size_id);
                return [
                    'id' => $variant->id,
                    'label' => $label
                ];
            })->values();
        });
        // $colors y $sizes ya calculados arriba
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', [
            'inventories' => $inventories,
            'variants' => $variants,
            'products' => $products,
            'variantsByProduct' => $variantsByProduct,
            'colors' => $colors,
            'sizes' => $sizes,
            'warehouses' => $warehouses
        ]);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $query = Inventory::with([
            'productVariant.product.tax',
            'productVariant.product.unitMeasure',
            'productVariant.product.brand',
            'productVariant.product.category',
            'warehouse'
        ]);
        // Filtros
        if ($request->filled('product_id')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            });
        }
        if ($request->filled('color_id')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('color_id', $request->input('color_id'));
            });
        }
        if ($request->filled('size_id')) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('size_id', $request->input('size_id'));
            });
        }
        if ($request->filled('product_variant_id')) {
            $query->where('product_variant_id', $request->input('product_variant_id'));
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('productVariant.product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        // Ordenamiento desde los <th>
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = [
            'id',
            'product_variant_id',
            'warehouse_id',
            'stock',
            'min_stock',
            'purchase_price',
            'sale_price',
            'created_at',
            'updated_at'
        ];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);
        $inventories = $query->paginate($perPage)->appends($request->all());
        $products = Product::where('status', 'available')->pluck('name', 'id');
        // Evitar duplicar eager loads innecesarios: no cargar color/size aquí
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })->get();
        $colorIds = $variants->pluck('color_id')->filter()->unique()->values();
        $sizeIds = $variants->pluck('size_id')->filter()->unique()->values();
        $colors = Color::whereIn('id', $colorIds)->pluck('name', 'id');
        $sizes = Size::whereIn('id', $sizeIds)->pluck('name', 'id');
        $variantsByProduct = $variants->groupBy('product_id')->map(function ($variants) use ($products, $colors, $sizes) {
            return $variants->map(function ($variant) use ($products, $colors, $sizes) {
                $label = $products->get($variant->product_id, (string) $variant->product_id);
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color_id)
                    $label .= ' / ' . ($colors[$variant->color_id] ?? $variant->color_id);
                if ($variant->size_id)
                    $label .= ' / ' . ($sizes[$variant->size_id] ?? $variant->size_id);
                return [
                    'id' => $variant->id,
                    'label' => $label
                ];
            })->values();
        });
        // $colors y $sizes ya calculados arriba
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', [
            'inventories' => $inventories,
            'products' => $products,
            'variantsByProduct' => $variantsByProduct,
            'colors' => $colors,
            'sizes' => $sizes,
            'warehouses' => $warehouses
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $variantId = $request->input('product_variant_id');
        $productId = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $stock = $request->input('stock');
        $minStock = $request->input('min_stock');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Inventory::with(['productVariant.product', 'warehouse']);
        if (!empty($productId)) {
            $query->whereHas('productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        } elseif (!empty($variantId)) {
            $query->where('product_variant_id', $variantId);
        }
        if (!empty($warehouseId)) {
            $query->where('warehouse_id', $warehouseId);
        }
        if (!empty($stock)) {
            $query->where('stock', $stock);
        }
        if (!empty($minStock)) {
            $query->where('min_stock', $minStock);
        }
        if (!empty($search)) {
            $query->whereHas('productVariant.product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        $allowedSorts = ['id', 'product_variant_id', 'warehouse_id', 'stock', 'min_stock', 'purchase_price', 'sale_price', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $timestamp = now()->format('Ymd_His');
        $filename = "inventarios_filtrados_{$timestamp}.xlsx";
        return Excel::download(new InventoriesExport($query), $filename);
    }

    public function create()
    {
        $this->authorize('create', Inventory::class);
        // Catálogo de productos para etiquetas sin recargar relaciones
        $products = Product::where('status', 'available')->pluck('name', 'id');
        $variantsAll = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })->get();
        $colorIds = $variantsAll->pluck('color_id')->filter()->unique()->values();
        $sizeIds = $variantsAll->pluck('size_id')->filter()->unique()->values();
        $colors = Color::whereIn('id', $colorIds)->pluck('name', 'id');
        $sizes = Size::whereIn('id', $sizeIds)->pluck('name', 'id');
        $variants = $variantsAll->mapWithKeys(function ($variant) use ($products, $colors, $sizes) {
            $label = $products->get($variant->product_id, (string) $variant->product_id);
            if ($variant->name)
                $label .= ' / ' . $variant->name;
            if ($variant->color_id)
                $label .= ' / ' . ($colors[$variant->color_id] ?? $variant->color_id);
            if ($variant->size_id)
                $label .= ' / ' . ($sizes[$variant->size_id] ?? $variant->size_id);
            return [$variant->id => $label];
        });
        // Agrupar variantes por producto para facilitar el filtrado en el frontend
        $variantsByProduct = $variantsAll
            ->groupBy('product_id')
            ->map(function ($variants) use ($products, $colors, $sizes) {
                return $variants->map(function ($variant) use ($products, $colors, $sizes) {
                    $label = $products->get($variant->product_id, (string) $variant->product_id);
                    if ($variant->name)
                        $label .= ' / ' . $variant->name;
                    if ($variant->color_id)
                        $label .= ' / ' . ($colors[$variant->color_id] ?? $variant->color_id);
                    if ($variant->size_id)
                        $label .= ' / ' . ($sizes[$variant->size_id] ?? $variant->size_id);
                    return [
                        'id' => $variant->id,
                        'label' => $label
                    ];
                })->values();
            });
        $warehouses = Warehouse::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        return view('admin.inventories.create', [
            'products' => $products,
            'variantsByProduct' => $variantsByProduct,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'brands' => $brands,
            'colors' => $colors,
            'sizes' => $sizes,
            'entities' => $entities,
        ]);
    }

    public function store(InventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);
        $data = $request->validated();

        \DB::beginTransaction();
        try {

            $inventory = Inventory::create($data);

            $isFirst = !InventoryMovement::whereHas('inventory', function ($q) use ($inventory) {
                $q->where('product_variant_id', $inventory->product_variant_id);
            })->exists();

            $inventory->inventoryMovements()->create([
                'type' => 'in',
                'quantity' => $inventory->stock,
                'unit_price' => $inventory->purchase_price ?? 0,
                'total_price' => ($inventory->purchase_price ?? 0) * $inventory->stock,
                'reference' => $isFirst ? 'Inventario Inicial' : 'Registro inicial',
                'notes' => $isFirst ? 'Primer registro de inventario para este producto' : 'Creación de inventario',
                'user_id' => auth()->id(),
            ]);

            \DB::commit();
            return redirect()->route('inventories.index')->with('success', 'Inventario creado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el inventario: ' . $e->getMessage()]);
        }
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);
        $inventory->load([
            'productVariant.product.tax',
            'productVariant.product.unitMeasure',
            'productVariant.product.brand',
            'productVariant.product.category',
            'warehouse'
        ]);
        return view('admin.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $inventory->load([
            'productVariant.product.tax',
            'productVariant.product.unitMeasure',
            'productVariant.product.brand',
            'productVariant.product.category',
            'warehouse'
        ]);
        $products = Product::where('status', 'available')->pluck('name', 'id');
        $variantsByProduct = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })
            ->with(['color', 'size'])
            ->get()
            ->groupBy('product_id')
            ->map(function ($variants) use ($products) {
                return $variants->map(function ($variant) use ($products) {
                    $label = $products->get($variant->product_id, (string) $variant->product_id);
                    if ($variant->name)
                        $label .= ' / ' . $variant->name;
                    if ($variant->color)
                        $label .= ' / ' . $variant->color->name;
                    if ($variant->size)
                        $label .= ' / ' . $variant->size->name;
                    return [
                        'id' => $variant->id,
                        'label' => $label
                    ];
                })->values();
            });
        $warehouses = Warehouse::pluck('name', 'id');
        // Movimientos recientes
        $movements = $inventory->inventoryMovements()->latest()->take(10)->get();
        return view('admin.inventories.edit', [
            'inventory' => $inventory,
            'products' => $products,
            'variantsByProduct' => $variantsByProduct,
            'warehouses' => $warehouses,
            'movements' => $movements
        ]);
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $data = $request->validated();

        \DB::beginTransaction();
        try {
            $inventory->update($data);

            if ($request->has('movement_type') && $request->input('movement_type')) {
                $type = $request->input('movement_type');
                $result = null;
                if ($type === 'transfer') {
                    $result = InventoryMovementManager::transfer($inventory, $request);
                } else if ($type === 'adjustment') {
                    $result = InventoryMovementManager::adjust($inventory, $request);
                }
                if (is_array($result) && isset($result['error'])) {
                    \DB::rollBack();
                    return back()->withErrors($result['error']);
                }
            }

            \DB::commit();
            return redirect()->route('inventories.index')->with('updated', 'Inventario actualizado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el inventario: ' . $e->getMessage()]);
        }
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('destroy', $inventory);
        $inventory->delete();
        return redirect()->route('inventories.index')->with('deleted', 'Inventario eliminado correctamente.');
    }

    /**
     * JSON search for Product Variants to pick before creating an inventory record.
     */
    public function variantSearch(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);

        $q = $request->string('q')->toString();
        $productId = $request->input('product_id');
        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');
        $entityId = $request->input('entity_id');
        $perPage = (int) $request->input('per_page', 10);

        $query = ProductVariant::query()
            ->with(['product.brand', 'product.category'])
            ->whereHas('product', function ($q2) {
                $q2->where('status', 'available');
            })
            ->whereDoesntHave('inventories');

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }
        if (!empty($colorId)) {
            $query->where('color_id', $colorId);
        }
        if (!empty($sizeId)) {
            $query->where('size_id', $sizeId);
        }
        if (!empty($categoryId)) {
            $query->whereHas('product', function ($sp) use ($categoryId) {
                $sp->where('category_id', $categoryId);
            });
        }
        if (!empty($brandId)) {
            $query->whereHas('product', function ($sp) use ($brandId) {
                $sp->where('brand_id', $brandId);
            });
        }
        if (!empty($entityId)) {
            $query->whereHas('product', function ($sp) use ($entityId) {
                $sp->where('entity_id', $entityId);
            });
        }
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('product', function ($sp) use ($q) {
                    $sp->where('name', 'like', "%{$q}%");
                })
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        $variants = $query->latest()->paginate($perPage);

        $colors = Color::whereIn('id', $variants->pluck('color_id')->filter()->unique()->values())
            ->pluck('name', 'id');
        $sizes = Size::whereIn('id', $variants->pluck('size_id')->filter()->unique()->values())
            ->pluck('name', 'id');

        $data = $variants->getCollection()->map(function ($v) use ($colors, $sizes) {
            return [
                'id' => $v->id,
                'product_id' => $v->product_id,
                'product_name' => optional($v->product)->name,
                'sku' => $v->sku ?? null,
                'barcode' => $v->barcode ?? null,
                'color_id' => $v->color_id,
                'color_name' => $v->color_id ? ($colors[$v->color_id] ?? null) : null,
                'size_id' => $v->size_id,
                'size_name' => $v->size_id ? ($sizes[$v->size_id] ?? null) : null,
                'category_name' => optional($v->product?->category)->name,
                'brand_name' => optional($v->product?->brand)->name,
                'label' => trim(sprintf(
                    '%s%s%s',
                    optional($v->product)->name,
                    $v->color_id ? (' - ' . ($colors[$v->color_id] ?? '-')) : '',
                    $v->size_id ? (' / ' . ($sizes[$v->size_id] ?? '-')) : ''
                )),
                'text' => trim(sprintf(
                    '%s%s%s',
                    optional($v->product)->name,
                    $v->color_id ? (' - ' . ($colors[$v->color_id] ?? '-')) : '',
                    $v->size_id ? (' / ' . ($sizes[$v->size_id] ?? '-')) : ''
                )),
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $variants->currentPage(),
                'last_page' => $variants->lastPage(),
                'per_page' => $variants->perPage(),
                'total' => $variants->total(),
            ],
        ]);
    }
}
