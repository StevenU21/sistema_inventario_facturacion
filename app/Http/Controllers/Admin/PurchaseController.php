<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Exports\PurchasesExport;
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

    public function create()
    {
        $this->authorize('create', Purchase::class);
        $entities = Entity::where('is_active', true)->where('is_supplier', true)->pluck('first_name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        return view('admin.purchases.create', compact('entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes'));
    }

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

    public function store(PurchaseRequest $request, PurchaseService $purchaseService)
    {
        $this->authorize('create', Purchase::class);
        return redirect()->route('purchases.index', )->with('success', 'Compra creada correctamente.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('view', $purchase);
        return view('admin.purchases.show', compact('purchase', 'details'));
    }

    public function edit(Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        return view('admin.purchases.edit', compact('purchase'));
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $this->authorize('update', $purchase);
        return redirect()->route('purchases.index')->with('updated', 'Compra actualizada.');
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorize('destroy', $purchase);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('deleted', 'Compra eliminada.');
    }
}
