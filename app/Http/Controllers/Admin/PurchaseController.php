<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseDetailRequest;
use App\Http\Requests\PurchaseRequest;
use App\Exports\PurchasesExport;
use App\Exports\PurchaseDetailsExport;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Entity;
use App\Models\Warehouse;
use App\Models\PaymentMethod;
use App\Services\PurchaseService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\UnitMeasure;
use App\Models\Tax;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        $query = Purchase::with(['entity', 'warehouse', 'user', 'paymentMethod', 'details.productVariant.product']);

        // Filtros básicos
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%$search%");
            });
        }
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }
        if ($methodId = $request->input('payment_method_id')) {
            $query->where('payment_method_id', $methodId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = (int) ($request->input('per_page', 10));
        $purchases = $query->latest()->paginate($perPage)->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        // Solo productos que han sido comprados (existen en detalles de compras)
        $productIds = PurchaseDetail::query()
            ->select('product_variant_id')
            ->whereNotNull('product_variant_id')
            ->distinct()
            ->pluck('product_variant_id');
        $products = Product::whereIn(
            'id',
            ProductVariant::whereIn('id', $productIds)->pluck('product_id')->unique()
        )->pluck('name', 'id');

        return view('admin.purchases.index', compact('purchases', 'entities', 'warehouses', 'methods', 'products'));
    }

    public function create()
    {
        $this->authorize('create', Purchase::class);
        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $product = null;
        $details = [];
        $prefillDetails = [];
        $allProducts = Product::pluck('name', 'id');
        return view('admin.purchases.create', compact('entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes', 'product', 'details', 'prefillDetails', 'allProducts'));
    }

    public function store(PurchaseRequest $request, PurchaseService $purchaseService)
    {
        $this->authorize('create', Purchase::class);
        $data = $request->validated();
        try {
            $purchaseService->createPurchase($data, $request->user());
            return redirect()->route('purchases.index')->with('success', 'Compra creada correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo guardar la compra: ' . $e->getMessage()]);
        }
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('view', $purchase);
        $purchase->load([
            'entity',
            'warehouse',
            'user',
            'paymentMethod',
            'details.productVariant.product',
            'details.productVariant.color',
            'details.productVariant.size',
        ]);
        $details = $purchase->details;
        $product = optional(optional($details->first())->productVariant)->product;

        // Preload all inventories for the variants in this purchase and warehouse
        $variantIds = $details->pluck('product_variant_id')->filter()->unique()->values();
        $inventories = \App\Models\Inventory::whereIn('product_variant_id', $variantIds)
            ->where('warehouse_id', $purchase->warehouse_id)
            ->get()
            ->keyBy('product_variant_id');

        // Prefill detalles desde inventario (sin N+1)
        $prefillDetails = $details->map(function ($d) use ($inventories) {
            $variant = $d->productVariant;
            $inventory = $variant ? $inventories->get($variant->id) : null;
            return [
                'color_id' => $variant?->color_id,
                'size_id' => $variant?->size_id,
                'quantity' => $d->quantity,
                'unit_price' => $d->unit_price,
                'sale_price' => optional($inventory)->sale_price,
                'min_stock' => optional($inventory)->min_stock,
            ];
        })->toArray();

        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        return view('admin.purchases.show', compact('purchase', 'product', 'details', 'prefillDetails', 'entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes'));
    }

    public function edit(Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        $purchase->load(['entity', 'warehouse', 'user', 'paymentMethod', 'details.productVariant.product']);
        $details = $purchase->details;
        $product = optional(optional($details->first())->productVariant)->product;
        // Prefill detalles desde inventario
        $prefillDetails = $details->map(function ($d) use ($purchase) {
            $variant = $d->productVariant;
            $inventory = \App\Models\Inventory::where('product_variant_id', $variant->id)
                ->where('warehouse_id', $purchase->warehouse_id)
                ->first();
            return [
                'color_id' => $variant->color_id,
                'size_id' => $variant->size_id,
                'quantity' => $d->quantity,
                'unit_price' => $d->unit_price,
                'sale_price' => optional($inventory)->sale_price,
                'min_stock' => optional($inventory)->min_stock,
            ];
        })->toArray();
        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $allProducts = Product::pluck('name', 'id');
        return view('admin.purchases.edit', compact('purchase', 'product', 'details', 'prefillDetails', 'entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes', 'allProducts'));
    }

    public function update(PurchaseRequest $request, Purchase $purchase, PurchaseService $purchaseService)
    {
        $this->authorize('update', $purchase);
        $data = $request->validated();
        try {
            $purchaseService->updatePurchase($purchase, $data, $request->user());
            return redirect()->route('purchases.index')->with('updated', 'Compra actualizada correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar la compra: ' . $e->getMessage()]);
        }
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorize('destroy', $purchase);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('deleted', 'Compra eliminada.');
    }

    // Búsqueda con filtros (misma vista que index)
    public function search(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        $query = $this->buildPurchasesQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $purchases = $query->latest()->paginate($perPage)->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        // Solo productos que han sido comprados (existen en detalles de compras)
        $productIds = PurchaseDetail::query()
            ->select('product_variant_id')
            ->whereNotNull('product_variant_id')
            ->distinct()
            ->pluck('product_variant_id');
        $products = Product::whereIn(
            'id',
            ProductVariant::whereIn('id', $productIds)->pluck('product_id')->unique()
        )->pluck('name', 'id');

        return view('admin.purchases.index', compact('purchases', 'entities', 'warehouses', 'methods', 'products'));
    }

    // Exportación a Excel usando los mismos filtros
    public function export(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        $query = $this->buildPurchasesQuery($request);
        $filename = 'compras_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PurchasesExport($query), $filename);
    }

    // Exporta el detalle completo de una compra específica
    public function exportDetails(Purchase $purchase)
    {
        $this->authorize('view', $purchase);
        $filename = 'compra_' . $purchase->id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PurchaseDetailsExport($purchase), $filename);
    }

    // Construye la consulta con todos los filtros soportados
    private function buildPurchasesQuery(Request $request)
    {
        $query = Purchase::with(['entity', 'warehouse', 'user', 'paymentMethod', 'details.productVariant.product']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%$search%");
            });
        }
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }
        if ($methodId = $request->input('payment_method_id')) {
            $query->where('payment_method_id', $methodId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($productId = $request->input('product_id')) {
            $query->whereHas('details.productVariant.product', function ($q) use ($productId) {
                $q->where('id', $productId);
            });
        }

        return $query;
    }

    // Devuelve productos filtrados para el modo "existing" (JSON)
    public function productSearch(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        // Debug: registrar filtros de entrada
        $filters = [
            'entity_id' => $request->input('entity_id'),
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            'warehouse_id' => $request->input('warehouse_id'),
            'q' => $request->input('q'),
        ];
        Log::debug('Purchases.productSearch: incoming filters', $filters);

        $warehouseId = $request->input('warehouse_id');
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        // Eager load mínimo requerido y, si corresponde, inventarios
        $with = [
            'brand:id,name',
            'category:id,name',
            'entity:id,first_name,last_name',
            'variants.inventories' => function ($inv) use ($warehouseId) {
                $inv->select('id', 'product_variant_id', 'warehouse_id', 'stock');
                if ($warehouseId) {
                    $inv->where('warehouse_id', $warehouseId);
                }
            },
        ];

        $q = Product::query()->with($with);

        if ($entityId = $request->input('entity_id')) {
            $q->where('entity_id', $entityId);
        }
        if ($categoryId = $request->input('category_id')) {
            $q->where('category_id', $categoryId);
        }
        if ($brandId = $request->input('brand_id')) {
            $q->where('brand_id', $brandId);
        }
        if ($term = trim((string) $request->input('q'))) {
            $q->where(function ($sub) use ($term) {
                $like = "%$term%";
                $sub->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('barcode', 'like', $like);
            });
        }

        if ($warehouseId) {
            // Asegura que el producto tenga inventario en el almacén seleccionado
            $q->whereHas('variants.inventories', function ($inv) use ($warehouseId) {
                $inv->where('warehouse_id', $warehouseId);
            });
            Log::debug('Purchases.productSearch: warehouse filter applied', ['warehouse_id' => $warehouseId]);
        }

        $started = microtime(true);
        $paginator = $q->latest()->paginate($perPage)->appends($request->query());
        $elapsedMs = round((microtime(true) - $started) * 1000, 1);

        // Transformar items para salida estructurada: cada campo en su propiedad
        $collection = $paginator->getCollection();
        $transformed = $collection->map(function ($p) use ($warehouseId) {
            $stock = 0;
            foreach ($p->variants as $variant) {
                foreach ($variant->inventories as $inv) {
                    $stock += (int) $inv->stock;
                }
            }
            $entity = $p->relationLoaded('entity') ? $p->entity : null;
            $entityName = $entity ? (trim($entity->short_name) ?: trim($entity->first_name)) : null;
            return [
                'id' => $p->id,
                'name' => $p->name,
                // compat con consumidores tipo select2
                'text' => $p->name,
                'brand_name' => optional($p->brand)->name,
                'category_name' => optional($p->category)->name,
                'code' => $p->code,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'stock' => $stock,
                'entity_id' => $p->entity_id,
                'entity_name' => $entityName,
                // útil para el front cuando ya hay un almacén filtrado
                'warehouse_id' => $warehouseId ? (int) $warehouseId : null,
            ];
        })->values();

        Log::debug('Purchases.productSearch: result', [
            'count' => $transformed->count(),
            'ids' => $collection->pluck('id')->take(50),
            'elapsed_ms' => $elapsedMs,
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);

        $meta = [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];

        // Si se solicita ?debug=1 o está activo app.debug, incluir bloque de debug
        if ($request->boolean('debug') || config('app.debug')) {
            return response()->json([
                'data' => $transformed,
                'meta' => $meta,
                'debug' => [
                    'filters' => $filters,
                    'count' => $transformed->count(),
                    'elapsed_ms' => $elapsedMs,
                ],
            ]);
        }

        return response()->json([
            'data' => $transformed,
            'meta' => $meta,
        ]);
    }
}
