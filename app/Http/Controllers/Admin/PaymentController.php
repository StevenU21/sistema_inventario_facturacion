<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Payment;
use App\Models\Entity;
use App\Models\PaymentMethod;
use App\Models\Company;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = $this->buildPaymentsQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $payments = $query->orderByDesc('payment_date')->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_client', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')), 'id');
        $methods = PaymentMethod::pluck('name', 'id');

        return view('admin.payments.index', compact('payments', 'entities', 'methods'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function export(Request $request)
    {
        $this->authorize('export', Payment::class);
        $query = $this->buildPaymentsQuery($request);
        $filename = 'pagos_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PaymentsExport($query), $filename);
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('export', Payment::class);
        $query = $this->buildPaymentsQuery($request);
        $payments = $query->orderByDesc('payment_date')->orderByDesc('id')->get();
        $company = Company::first();

        $pdf = Pdf::loadView('admin.payments.report', [
            'payments' => $payments,
            'company' => $company,
            'filters' => $request->all(),
        ])->setPaper('letter');

        return $pdf->download('pagos_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildPaymentsQuery(Request $request)
    {
        $query = Payment::with(['entity', 'paymentMethod', 'user', 'accountReceivable.sale']);

        // Búsqueda general por cliente, ID de pago o ID de venta
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('entity', function ($qe) use ($search) {
                        $qe->whereRaw("TRIM(CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) LIKE ?", ['%' . $search . '%'])
                           ->orWhere('short_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('accountReceivable.sale', function ($qs) use ($search) {
                        $qs->where('id', $search);
                    });
            });
        }

        // Filtros
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($methodId = $request->input('payment_method_id')) {
            $query->where('payment_method_id', $methodId);
        }
        if ($saleId = $request->input('sale_id')) {
            $query->whereHas('accountReceivable', function ($q) use ($saleId) {
                $q->where('sale_id', $saleId);
            });
        }
        if ($from = $request->input('from')) {
            $query->whereDate('payment_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('payment_date', '<=', $to);
        }
        if (!is_null($request->input('min_amount'))) {
            $query->where('amount', '>=', (float) $request->input('min_amount'));
        }
        if (!is_null($request->input('max_amount'))) {
            $query->where('amount', '<=', (float) $request->input('max_amount'));
        }

        return $query;
    }
}
