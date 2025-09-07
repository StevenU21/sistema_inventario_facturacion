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

        // Para el selector: solo productos con inventario
        $products = Product::whereIn('id', function ($q) {
            $q->select('product_id')->from('inventories');
        })->orderBy('name')->pluck('name', 'id');

        $warehouses = Warehouse::orderBy('name')->pluck('name', 'id');

        $kardexModel = null;
        if ($productId) {
            $kardexModel = $kardex->generate((int) $productId, $warehouseId ? (int) $warehouseId : null, $from, $to);
        }

        return view('admin.kardex.index', compact('products', 'warehouses', 'kardexModel', 'productId', 'warehouseId', 'from', 'to'));
    }

    public function exportPdf(Request $request, KardexService $kardex)
    {
        $productId = (int) $request->input('product_id');
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $from = $request->input('from');
        $to = $request->input('to');

        $kardexModel = $kardex->generate($productId, $warehouseId, $from, $to);

        $company = Company::first();
        $data = [
            'kardexModel' => $kardexModel,
            'company' => $company
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.kardex.pdf', $data)->setPaper('a4', 'landscape');
        $productId = '';
        if ($kardexModel && is_object($kardexModel->product) && isset($kardexModel->product->id)) {
            $productId = $kardexModel->product->id;
        }
        $filename = 'kardex_' . $productId . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}
