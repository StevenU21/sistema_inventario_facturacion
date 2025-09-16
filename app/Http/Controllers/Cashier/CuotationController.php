<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Services\QuotationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CuotationController extends Controller
{
    use AuthorizesRequests;

    public function store(QuotationRequest $request, QuotationService $service)
    {
        // Autorización si hay policy para Quotation (o se puede omitir si no persiste)
        // $this->authorize('create', Quotation::class);

        $result = $service->createQuotation($request->validated());
        return $result['pdf']->stream('proforma.pdf');
    }
}
