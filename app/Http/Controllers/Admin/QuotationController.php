<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Quotation;
use App\Models\Entity;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Services\QuotationService;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);

        $query = $this->buildQuotationsQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $quotations = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());

        $entities = Entity::where('is_active', true)->get()
            ->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');

        return view('admin.quotations.index', compact('quotations', 'entities'));
    }

    public function create()
    {
        $this->authorize('create', Quotation::class);

        $entities = Entity::where('is_active', true)->where('is_client', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $suppliers = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');

        return view('admin.quotations.create', compact('entities', 'warehouses', 'categories', 'brands', 'colors', 'sizes', 'suppliers'));
    }

    public function store(Request $request, QuotationService $service)
    {
        $this->authorize('create', Quotation::class);

        $validated = $request->validate([
            'entity_id' => ['nullable', 'integer', 'exists:entities,id', 'required_without:client.first_name'],
            'client.first_name' => ['nullable', 'string', 'max:255', 'required_without:entity_id'],
            'client.last_name' => ['nullable', 'string', 'max:255'],
            'client.identity_card' => ['nullable', 'string', 'max:255'],
            'client.ruc' => ['nullable', 'string', 'max:255'],
            'client.email' => ['nullable', 'email', 'max:255'],
            'client.phone' => ['nullable', 'string', 'max:255'],
            'client.address' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount' => ['nullable', 'boolean'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            // Crear cliente en caliente si es necesario
            if (empty($validated['entity_id']) && !empty($validated['client']['first_name'] ?? null)) {
                $c = $validated['client'];
                $entity = Entity::create([
                    'first_name' => $c['first_name'],
                    'last_name' => $c['last_name'] ?? null,
                    'identity_card' => $c['identity_card'] ?? null,
                    'ruc' => $c['ruc'] ?? null,
                    'email' => $c['email'] ?? null,
                    'phone' => $c['phone'] ?? null,
                    'address' => $c['address'] ?? null,
                    'is_client' => true,
                    'is_supplier' => false,
                    'is_active' => true,
                ]);
                $validated['entity_id'] = $entity->id;
            }
            $result = $service->storeQuotation($validated);
            /** @var \App\Models\Quotation $quotation */
            $quotation = $result['quotation'];
            return redirect()->route('admin.quotations.pdf', $quotation)->with('success', 'Cotización registrada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar cotización', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ocurrió un error al registrar la cotización: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('export', Quotation::class);
        $query = $this->buildQuotationsQuery($request);
        $quotations = $query->orderByDesc('created_at')->orderByDesc('id')->get();
        $company = Company::first();

        $pdf = Pdf::loadView('admin.quotations.report', [
            'quotations' => $quotations,
            'company' => $company,
            'filters' => $request->all(),
        ])->setPaper('letter');

        return $pdf->download('cotizaciones_' . now()->format('Ymd_His') . '.pdf');
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorize('view', $quotation);
        $quotation->load(['QuotationDetails.productVariant.product.tax', 'user', 'entity']);

        $company = Company::first();
        $details = [];
        foreach ($quotation->QuotationDetails as $qd) {
            $variant = $qd->productVariant;
            $product = $variant?->product;
            $tax = $product?->tax;
            $salePrice = (float) $qd->unit_price; // ya incluye impuesto en nuestro flujo de cálculo
            $taxPercentage = $tax ? (float) $tax->percentage : null;
            $unitTaxAmount = $taxPercentage ? round(($salePrice / (1 + $taxPercentage / 100)) * ($taxPercentage / 100), 2) : 0.0;

            $details[] = [
                'variant' => $variant,
                'inventory' => null,
                'quantity' => (int) $qd->quantity,
                'unit_price' => (float) $qd->unit_price,
                'sub_total' => (float) $qd->sub_total,
                'discount' => (bool) $qd->discount,
                'discount_amount' => (float) $qd->discount_amount,
                'unit_tax_amount' => $unitTaxAmount,
                'tax_percentage' => $taxPercentage,
            ];
        }

        $total = array_sum(array_column($details, 'sub_total'));
        $totalTax = 0.0;
        foreach ($details as $d) {
            $totalTax += round($d['unit_tax_amount'] * $d['quantity'], 2);
        }

        $totals = [
            'sub_total' => max(0, $total - $totalTax),
            'total' => $total,
            'totalTax' => $totalTax,
        ];

        $pdf = Pdf::loadView('cashier.quotations.proforma', [
            'company' => $company,
            'entity' => $quotation->entity,
            'details' => $details,
            'totals' => $totals,
            'quotation_date' => optional($quotation->created_at)?->toDateString(),
            'user' => $quotation->user,
            'quotation' => $quotation,
        ])->setPaper('letter');

        return $pdf->stream('cotizacion_' . $quotation->id . '.pdf');
    }

    // JSON: búsqueda basada en inventarios para crear cotizaciones
    public function productSearch(Request $request)
    {
        $this->authorize('create', Quotation::class);

        $q = $request->string('q')->toString();
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');
        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $warehouseId = $request->input('warehouse_id');
        $perPage = (int) $request->input('per_page', 5);

        $query = Inventory::query()
            ->with([
                'warehouse',
                'productVariant.product.tax',
                'productVariant.product.brand.category',
                'productVariant.color',
                'productVariant.size',
            ])
            ->whereHas('productVariant.product', function ($q2) {
                $q2->where('status', 'available');
            });

        if (!empty($warehouseId)) {
            $query->where('warehouse_id', $warehouseId);
        }
        if (!empty($categoryId)) {
            $query->whereHas('productVariant.product.brand', function ($sp) use ($categoryId) {
                $sp->where('category_id', $categoryId);
            });
        }
        if (!empty($brandId)) {
            $query->whereHas('productVariant.product', function ($sp) use ($brandId) {
                $sp->where('brand_id', $brandId);
            });
        }
        if (!empty($colorId)) {
            $query->whereHas('productVariant', function ($sp) use ($colorId) {
                $sp->where('color_id', $colorId);
            });
        }
        if (!empty($sizeId)) {
            $query->whereHas('productVariant', function ($sp) use ($sizeId) {
                $sp->where('size_id', $sizeId);
            });
        }
        if (!empty($q)) {
            $query->whereHas('productVariant.product', function ($sp) use ($q) {
                $sp->where('name', 'like', "%{$q}%");
            });
        }

        $inventories = $query->latest()->paginate($perPage);

        $data = $inventories->getCollection()->map(function ($inv) {
            $variant = $inv->productVariant;
            $product = optional($variant)->product;
            $warehouse = $inv->warehouse;
            $salePrice = (float) ($inv->sale_price ?? 0);
            $taxPercentage = optional($product?->tax)->percentage;
            $taxPercentage = $taxPercentage !== null ? (float) $taxPercentage : null;
            $unitTaxAmount = $taxPercentage ? round($salePrice * ($taxPercentage / 100), 2) : 0.0;
            $unitPriceWithTax = round($salePrice + $unitTaxAmount, 2);

            return [
                'id' => $inv->id,
                'product_variant_id' => optional($variant)->id,
                'product_id' => optional($variant)->product_id,
                'product_name' => optional($product)->name,
                'color_id' => optional($variant)->color_id,
                'color_name' => optional($variant?->color)->name,
                'size_id' => optional($variant)->size_id,
                'size_name' => optional($variant?->size)->name,
                'category_name' => optional($product?->brand?->category)->name,
                'brand_name' => optional($product?->brand)->name,
                'warehouse_id' => $inv->warehouse_id,
                'warehouse_name' => optional($warehouse)->name,
                'stock' => (int) ($inv->stock ?? 0),
                'sale_price' => $salePrice,
                'unit_price_with_tax' => $unitPriceWithTax,
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $inventories->currentPage(),
                'last_page' => $inventories->lastPage(),
                'per_page' => $inventories->perPage(),
                'total' => $inventories->total(),
            ],
        ]);
    }

    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        $entityIds = Quotation::query()->pluck('entity_id')->unique()->values();
        $entitiesQuery = Entity::query()->whereIn('id', $entityIds)->where('is_client', true);

        if ($term !== '') {
            $like = "%{$term}%";
            $entitiesQuery->where(function ($q) use ($like) {
                $q->whereRaw("TRIM(COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')) LIKE ?", [$like])
                    ->orWhere('short_name', 'like', $like);
            });
        }

        $entities = $entitiesQuery->select(['id', 'first_name', 'last_name', 'short_name'])
            ->orderBy('first_name')
            ->limit($limit)
            ->get();

        $suggestions = $entities->map(fn($e) => [
            'id' => $e->id,
            'text' => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')) ?: ($e->short_name ?? ''),
        ]);

        return response()->json(['data' => $suggestions]);
    }

    private function buildQuotationsQuery(Request $request)
    {
        $query = Quotation::with(['entity', 'user', 'QuotationDetails.productVariant.product']);

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('entity', function ($qe) use ($search) {
                        $qe->whereRaw("TRIM(COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')) LIKE ?", ['%' . $search . '%'])
                            ->orWhere('short_name', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
