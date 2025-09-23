<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kardex;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\KardexService;
use App\Models\Company;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Entity;
// use Barryvdh\DomPDF\Facade\Pdf; // Usaremos el contenedor para evitar dependencias directas
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KardexController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request, KardexService $kardex)
    {
        $this->authorize('viewAny', Kardex::class); // o una policy específica

        $productId = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $metodo = $request->input('metodo', 'cpp');
        // Nueva opción: generar kardex por variante específica
        $productVariantId = $request->input('product_variant_id');

        // Para el selector: solo productos con variantes en inventario
        $products = Product::whereIn('id', function ($q) {
            $q->select('product_id')
                ->from('product_variants')
                ->whereIn('id', function ($sq) {
                    $sq->select('product_variant_id')->from('inventories');
                });
        })->orderBy('name')->pluck('name', 'id');

        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');
        // Selects globales de color y talla
        $variants = ProductVariant::with(['color', 'size'])->get();
        $colors = $variants->pluck('color')->filter()->unique('id')->mapWithKeys(fn($c) => [$c->id => $c->name]);
        $sizes = $variants->pluck('size')->filter()->unique('id')->mapWithKeys(fn($s) => [$s->id => $s->name]);

        $kardexModel = null;
        if ($productId) {
            $kardexModel = $kardex->generate(
                (int) $productId,
                $warehouseId ? (int) $warehouseId : null,
                $from,
                $to,
                $metodo,
                $colorId ? (int) $colorId : null,
                $sizeId ? (int) $sizeId : null,
                $productVariantId ? (int) $productVariantId : null
            );
        }
        // Catálogos adicionales
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        // Mapeo de marcas por categoría para filtro dependiente
        $brandsByCategory = Brand::with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($grp) => $grp->pluck('name', 'id'))
            ->toArray();
        $entities = Entity::where('is_active', true)->where('is_supplier', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');

        return view('admin.kardex.index', compact(
            'products',
            'warehouses',
            'colors',
            'sizes',
            'kardexModel',
            'productId',
            'warehouseId',
            'from',
            'to',
            'metodo',
            'colorId',
            'sizeId',
            'productVariantId',
            'categories',
            'brands',
            'brandsByCategory',
            'entities'
        ));
    }

    public function exportPdf(Request $request, KardexService $kardex)
    {
        $this->authorize('export', Kardex::class); // o una policy específica
        $productId = (int) $request->input('product_id');
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $from = $request->input('from');
        $to = $request->input('to');
        $metodo = $request->input('metodo', 'cpp');
        $productVariantId = $request->input('product_variant_id');

        $colorId = $request->filled('color_id') ? (int) $request->input('color_id') : null;
        $sizeId = $request->filled('size_id') ? (int) $request->input('size_id') : null;
        $kardexModel = $kardex->generate($productId, $warehouseId, $from, $to, $metodo, $colorId, $sizeId, $productVariantId ? (int) $productVariantId : null);

        $company = Company::first();
        $data = [
            'kardexModel' => $kardexModel,
            'company' => $company,
            'metodo' => $metodo
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.kardex.pdf', $data)->setPaper('a4', 'landscape');

        // Personalizar nombre según método
        $metodoNombre = [
            'cpp' => 'Promedio_Ponderado',
            'peps' => 'PEPS_FIFO',
            'ueps' => 'UEPS_LIFO',
        ][$metodo] ?? 'Promedio_Ponderado';

        $fechaRango = '';
        if ($from && $to) {
            $fechaRango = '_' . str_replace('-', '', $from) . '_a_' . str_replace('-', '', $to);
        } elseif ($from) {
            $fechaRango = '_desde_' . str_replace('-', '', $from);
        } elseif ($to) {
            $fechaRango = '_hasta_' . str_replace('-', '', $to);
        }

        $productoNombre = '';
        if ($kardexModel && is_object($kardexModel->product) && isset($kardexModel->product->name)) {
            $productoNombre = '_' . preg_replace('/[^A-Za-z0-9]/', '', $kardexModel->product->name);
        }

        $filename = 'Kardex_' . $metodoNombre . $productoNombre . $fechaRango . '.pdf';
        return $pdf->download($filename);
    }
    /**
     * AJAX endpoint: generate Kardex without full page reload
     */
    public function generateAjax(Request $request, KardexService $kardex)
    {
        $this->authorize('create', Kardex::class);
        $productId = (int) $request->input('product_id');
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $from = $request->input('from');
        $to = $request->input('to');
        $metodo = $request->input('metodo', 'cpp');
        $colorId = $request->filled('color_id') ? (int) $request->input('color_id') : null;
        $sizeId = $request->filled('size_id') ? (int) $request->input('size_id') : null;
        $productVariantId = $request->filled('product_variant_id') ? (int) $request->input('product_variant_id') : null;
        $model = $kardex->generate($productId, $warehouseId, $from, $to, $metodo, $colorId, $sizeId, $productVariantId);
        return response()->json([
            'product' => $model->product?->name,
            'warehouse' => $model->warehouse?->name,
            'date_from' => $model->date_from,
            'date_to' => $model->date_to,
            'method' => $metodo,
            'rows' => $model->rows,
            'final' => $model->final,
        ]);
    }
}
