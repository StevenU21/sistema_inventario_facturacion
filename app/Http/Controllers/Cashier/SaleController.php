<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Services\SaleService;

class SaleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Sale::class);
        return view('cashier.sales.index');
    }

    public function store(SaleRequest $request, SaleService $saleService)
    {
        $this->authorize('create', Sale::class);
        $result = $saleService->createSale($request->validated());

        return $result['pdf']->stream('venta_' . $result['sale']->id . '.pdf');
    }
}
