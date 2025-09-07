<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\KardexService;
use App\Models\Company;
// use Barryvdh\DomPDF\Facade\Pdf; // Usaremos el contenedor para evitar dependencias directas
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KardexController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request, KardexService $kardex)
    {
        $this->authorize('viewAny', Product::class); // o una policy específica

        $productId = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $metodo = $request->input('metodo', 'cpp');

        // Para el selector: solo productos con inventario
        $products = Product::whereIn('id', function ($q) {
            $q->select('product_id')->from('inventories');
        })->orderBy('name')->pluck('name', 'id');

        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');

        $kardexModel = null;
        if ($productId) {
            $kardexModel = $kardex->generate((int) $productId, $warehouseId ? (int) $warehouseId : null, $from, $to, $metodo);
        }

        return view('admin.kardex.index', compact('products', 'warehouses', 'kardexModel', 'productId', 'warehouseId', 'from', 'to', 'metodo'));
    }

    public function exportPdf(Request $request, KardexService $kardex)
    {
        $productId = (int) $request->input('product_id');
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $from = $request->input('from');
        $to = $request->input('to');
        $metodo = $request->input('metodo', 'cpp');

        $kardexModel = $kardex->generate($productId, $warehouseId, $from, $to, $metodo);

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
            $fechaRango = '_'.str_replace('-', '', $from).'_a_'.str_replace('-', '', $to);
        } elseif ($from) {
            $fechaRango = '_desde_'.str_replace('-', '', $from);
        } elseif ($to) {
            $fechaRango = '_hasta_'.str_replace('-', '', $to);
        }

        $productoNombre = '';
        if ($kardexModel && is_object($kardexModel->product) && isset($kardexModel->product->name)) {
            $productoNombre = '_'.preg_replace('/[^A-Za-z0-9]/', '', $kardexModel->product->name);
        }

        $filename = 'Kardex_' . $metodoNombre . $productoNombre . $fechaRango . '.pdf';
        return $pdf->download($filename);
    }
}
