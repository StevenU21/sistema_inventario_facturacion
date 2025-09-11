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

        return view('admin.purchases.index', compact('purchases', 'entities', 'warehouses', 'methods'));
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
        return view('admin.purchases.create', compact('entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes', 'product', 'details', 'prefillDetails'));
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
        return view('admin.purchases.edit', compact('purchase', 'product', 'details', 'prefillDetails', 'entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes'));
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
        $products = Product::pluck('name', 'id');

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
}
