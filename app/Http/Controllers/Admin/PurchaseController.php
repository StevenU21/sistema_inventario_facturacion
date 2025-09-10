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
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

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
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        return view('admin.purchases.create', compact('entities', 'warehouses', 'methods', 'categories', 'brands', 'units', 'taxes', 'colors', 'sizes'));
    }

    public function store(PurchaseRequest $request)
    {
        $this->authorize('create', Purchase::class);
        $data = $request->validated();
        // Ensure sensitive/derived fields are set server-side
        $data['user_id'] = auth()->id();
        $data['subtotal'] = 0;
        $data['total'] = 0;
        $purchase = Purchase::create($data);

        // Inline product + variants creation (optional)
        $lines = $request->input('lines', []);
        $creatingProduct = $request->filled('product_name');

        if ($creatingProduct) {
            // Minimal validation for product when present
            $request->validate([
                'product_name' => ['required', 'string', 'min:2', 'max:255'],
                'product_category_id' => ['required', 'exists:categories,id'],
                'product_brand_id' => ['required', 'exists:brands,id'],
                'product_unit_measure_id' => ['required', 'exists:unit_measures,id'],
                'product_tax_id' => ['required', 'exists:taxes,id'],
            ]);

            $product = Product::create([
                'name' => $request->input('product_name'),
                'description' => $request->input('product_description'),
                'barcode' => $request->input('product_barcode'),
                'status' => 'available',
                'brand_id' => (int) $request->input('product_brand_id'),
                'category_id' => (int) $request->input('product_category_id'),
                'tax_id' => (int) $request->input('product_tax_id'),
                'unit_measure_id' => (int) $request->input('product_unit_measure_id'),
                'entity_id' => $purchase->entity_id,
            ]);

            // Ensure at least a simple variant exists if no lines
            if (empty($lines)) {
                ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'color_id' => null,
                    'size_id' => null,
                ]);
            }

            // For each line, create/find variant and detail
            foreach ($lines as $line) {
                $qty = (int) ($line['quantity'] ?? 0);
                $price = (float) ($line['unit_price'] ?? 0);
                $colorId = $line['color_id'] !== '' ? ($line['color_id'] ?? null) : null;
                $sizeId = $line['size_id'] !== '' ? ($line['size_id'] ?? null) : null;
                if ($qty <= 0)
                    continue;
                $variant = ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                ]);
                $detail = PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                ]);
                $this->purchaseService->applyDetailToInventory($purchase, $detail);
            }

            $this->purchaseService->recalculateTotals($purchase);
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Compra creada correctamente.');
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
        // Variants select options (Product name + Color/Talla o Simple)
        $variants = ProductVariant::with(['product', 'color', 'size'])
            ->get()
            ->mapWithKeys(function ($v) {
                $label = $v->product->name;
                $c = $v->color->name ?? null;
                $s = $v->size->name ?? null;
                $label .= ' - ' . ($c || $s ? (($c ?: '—') . ' / ' . ($s ?: '—')) : 'Simple');
                return [$v->id => $label];
            });
        return view('admin.purchases.edit', compact('purchase', 'entities', 'warehouses', 'methods', 'details', 'variants'));
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
}
