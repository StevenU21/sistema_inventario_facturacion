<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseDetailRequest;
use App\Exports\PurchasesExport;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Entity;
use App\Models\Warehouse;
use App\Models\PaymentMethod;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        $query = Purchase::with(['entity', 'warehouse', 'user', 'paymentMethod', 'details.productVariant.product']);

        // Filters
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

        // Build product options from currently filtered purchases (before applying product filter)
        $basePurchaseIds = (clone $query)->pluck('id');
        $productIds = PurchaseDetail::whereIn('purchase_id', $basePurchaseIds)
            ->join('product_variants', 'purchase_details.product_variant_id', '=', 'product_variants.id')
            ->pluck('product_variants.product_id')
            ->unique()
            ->values();
        $products = Product::whereIn('id', $productIds)->orderBy('name')->pluck('name', 'id');

        if ($productId = $request->input('product_id')) {
            $query->whereHas('details.productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        $perPage = (int) ($request->input('per_page', 10));
        $purchases = $query->latest()->paginate($perPage)->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');

        return view('admin.purchases.index', compact('purchases', 'entities', 'warehouses', 'methods', 'products'));
    }

    public function search(Request $request)
    {
        // For now, reuse index to handle filters/search
        return $this->index($request);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
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
            $query->whereHas('details.productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "compras_filtradas_{$timestamp}.xlsx";
        return Excel::download(new PurchasesExport($query), $filename);
    }

    public function create()
    {
        $this->authorize('create', Purchase::class);
        $entities = Entity::where('is_active', true)->where('is_supplier', true)->pluck('first_name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        return view('admin.purchases.create', compact('entities', 'warehouses', 'methods'));
    }

    public function store(PurchaseRequest $request)
    {
        $this->authorize('create', Purchase::class);
        $data = $request->validated();
        $purchase = Purchase::create($data);
        return redirect()->route('purchases.edit', $purchase)->with('success', 'Compra creada. Agregue los detalles.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('view', $purchase);
        $purchase->load(['entity', 'warehouse', 'user', 'paymentMethod']);
        $details = PurchaseDetail::with('productVariant.product')->where('purchase_id', $purchase->id)->get();
        return view('admin.purchases.show', compact('purchase', 'details'));
    }

    public function edit(Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        $entities = Entity::where('is_active', true)->where('is_supplier', true)->pluck('first_name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $details = PurchaseDetail::with('productVariant.product')->where('purchase_id', $purchase->id)->get();
        return view('admin.purchases.edit', compact('purchase', 'entities', 'warehouses', 'methods', 'details'));
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        $data = $request->validated();
        $purchase->update($data);
        return redirect()->route('purchases.index')->with('updated', 'Compra actualizada.');
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorize('destroy', $purchase);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('deleted', 'Compra eliminada.');
    }

    // Details management
    public function addDetail(PurchaseDetailRequest $request, Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        $data = $request->validated();
        $data['purchase_id'] = $purchase->id;
        $detail = PurchaseDetail::create($data);
        $this->applyDetailToInventory($purchase, $detail);
        $this->recalculateTotals($purchase);
        return back()->with('success', 'Detalle agregado.');
    }

    public function removeDetail(Purchase $purchase, PurchaseDetail $detail)
    {
        $this->authorize('update', $purchase);
        if ($detail->purchase_id !== $purchase->id) {
            abort(404);
        }
        $this->revertDetailFromInventory($purchase, $detail);
        $detail->delete();
        $this->recalculateTotals($purchase);
        return back()->with('deleted', 'Detalle eliminado.');
    }

    private function applyDetailToInventory(Purchase $purchase, PurchaseDetail $detail): void
    {
        $inventory = Inventory::firstOrCreate(
            [
                'product_variant_id' => $detail->product_variant_id,
                'warehouse_id' => $purchase->warehouse_id,
            ],
            [
                'stock' => 0,
                'min_stock' => 0,
                'purchase_price' => $detail->unit_price,
                'sale_price' => round($detail->unit_price * 1.3, 2),
            ]
        );
        $inventory->stock += $detail->quantity;
        $inventory->purchase_price = $detail->unit_price;
        $inventory->save();
        InventoryMovement::create([
            'type' => 'in',
            'adjustment_reason' => null,
            'quantity' => $detail->quantity,
            'unit_price' => $detail->unit_price,
            'total_price' => $detail->quantity * $detail->unit_price,
            'reference' => $purchase->reference,
            'notes' => 'Entrada por compra (CRUD)',
            'user_id' => auth()->id(),
            'inventory_id' => $inventory->id,
        ]);
    }

    private function revertDetailFromInventory(Purchase $purchase, PurchaseDetail $detail): void
    {
        $inventory = Inventory::where('product_variant_id', $detail->product_variant_id)
            ->where('warehouse_id', $purchase->warehouse_id)
            ->first();
        if (!$inventory)
            return;
        $inventory->stock = max(0, $inventory->stock - $detail->quantity);
        $inventory->save();
        InventoryMovement::create([
            'type' => 'out',
            'adjustment_reason' => null,
            'quantity' => $detail->quantity,
            'unit_price' => $detail->unit_price,
            'total_price' => $detail->quantity * $detail->unit_price,
            'reference' => $purchase->reference,
            'notes' => 'Reversión de detalle de compra (CRUD)',
            'user_id' => auth()->id(),
            'inventory_id' => $inventory->id,
        ]);
    }

    private function recalculateTotals(Purchase $purchase): void
    {
        $subtotal = PurchaseDetail::where('purchase_id', $purchase->id)
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as subtotal')
            ->value('subtotal');
        $purchase->subtotal = $subtotal;
        $purchase->total = $subtotal;
        $purchase->save();
    }
}
