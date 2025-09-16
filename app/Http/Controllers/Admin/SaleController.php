<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SaleDetailsExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Entity;
use App\Models\PaymentMethod;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Company;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\ProductVariant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $query = $this->buildSalesQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $sales = $query->latest()->paginate($perPage)->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_client', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');

        return view('admin.sales.index', compact('sales', 'entities', 'methods', 'brands', 'colors', 'sizes'));
    }

    // Búsqueda con filtros (misma vista que index)
    public function search(Request $request)
    {
        $this->authorize('viewAny', Sale::class);
        $query = $this->buildSalesQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $sales = $query->latest()->paginate($perPage)->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_client', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $methods = PaymentMethod::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');

        return view('admin.sales.index', compact('sales', 'entities', 'methods', 'brands', 'colors', 'sizes'));
    }

    // Exportación a Excel usando los mismos filtros
    public function export(Request $request)
    {
        $this->authorize('export', Sale::class);
        $query = $this->buildSalesQuery($request);
        $filename = 'ventas_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SalesExport($query), $filename);
    }

    // Exporta el detalle completo de una venta específica
    public function exportDetails(Sale $sale)
    {
        $this->authorize('view', $sale);
        $filename = 'venta_' . $sale->id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new SaleDetailsExport($sale), $filename);
    }

    // Genera el PDF de una venta específica (factura)
    public function pdf(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['saleDetails.productVariant.product.tax', 'user', 'entity', 'paymentMethod']);
        $pdf = Pdf::loadView('cashier.sales.invoice', [
            'sale' => $sale,
            'company' => Company::first(),
            'details' => $sale->saleDetails,
        ])->setPaper('letter');

        return $pdf->stream('venta_' . $sale->id . '.pdf');
    }

    // Endpoint para autocompletar en índices (ventas)
    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', Sale::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        // Solo productos que han sido vendidos (existen en detalles de ventas)
        $productIds = SaleDetail::query()
            ->join('product_variants as pv', 'pv.id', '=', 'sale_details.product_variant_id')
            ->whereNotNull('pv.product_id')
            ->pluck('pv.product_id')
            ->unique()
            ->values();

        $productsQuery = Product::query()->whereIn('id', $productIds);

        if ($term !== '') {
            $like = "%{$term}%";
            $productsQuery->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('barcode', 'like', $like);
            });
        }

        $products = $productsQuery->select(['id', 'name'])->orderBy('name')->limit($limit)->get();

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

    // Construye la consulta con todos los filtros soportados
    private function buildSalesQuery(Request $request)
    {
        $query = Sale::with(['entity', 'user', 'paymentMethod', 'saleDetails.productVariant.product.brand', 'saleDetails.productVariant.color', 'saleDetails.productVariant.size']);

        // Búsqueda por nombre de producto
        if ($search = $request->input('search')) {
            $like = '%' . trim($search) . '%';
            $query->whereHas('saleDetails.productVariant.product', function ($q) use ($like) {
                $q->where('name', 'like', $like);
            });
        }

        // Filtros adicionales
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($methodId = $request->input('payment_method_id')) {
            $query->where('payment_method_id', $methodId);
        }
        if ($isCredit = $request->input('is_credit')) {
            // acepta '1'/'0' o true/false
            if ($isCredit === '1' || $isCredit === 1 || $isCredit === true || $isCredit === 'true') {
                $query->where('is_credit', true);
            } elseif ($isCredit === '0' || $isCredit === 0 || $isCredit === false || $isCredit === 'false') {
                $query->where('is_credit', false);
            }
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($brandId = $request->input('brand_id')) {
            $query->whereHas('saleDetails.productVariant.product', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }
        if ($colorId = $request->input('color_id')) {
            $query->whereHas('saleDetails.productVariant', function ($q) use ($colorId) {
                $q->where('color_id', $colorId);
            });
        }
        if ($sizeId = $request->input('size_id')) {
            $query->whereHas('saleDetails.productVariant', function ($q) use ($sizeId) {
                $q->where('size_id', $sizeId);
            });
        }

        return $query;
    }
}
