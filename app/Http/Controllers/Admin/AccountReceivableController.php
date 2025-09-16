<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\AccountReceivable;
use App\Models\Entity;
use App\Models\Company;
use App\Exports\AccountReceivablesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountReceivableController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountReceivable::class);

        $query = $this->buildAccountsReceivableQuery($request);
        $perPage = (int) ($request->input('per_page', 10));
        $accounts = $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());

        $entities = Entity::where('is_active', true)->where('is_client', true)
            ->get()->pluck(fn($e) => trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')) ?: ($e->short_name ?? ''), 'id');

        $statuses = [
            'pending' => __('Pendiente'),
            'partially_paid' => __('Parcialmente pagado'),
            'paid' => __('Pagado'),
        ];

        return view('admin.accounts_receivable.index', compact('accounts', 'entities', 'statuses'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function show(AccountReceivable $accountReceivable)
    {
        $this->authorize('view', $accountReceivable);
        $accountReceivable->load([
            'entity',
            'sale.saleDetails.productVariant.product',
            'payments.paymentMethod',
            'payments.user',
        ]);

        return view('admin.accounts_receivable.show', [
            'ar' => $accountReceivable,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('export', AccountReceivable::class);
        $query = $this->buildAccountsReceivableQuery($request);
        $filename = 'cuentas_por_cobrar_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AccountReceivablesExport($query), $filename);
    }

    public function exportPdf(AccountReceivable $accountReceivable)
    {
        $this->authorize('export', AccountReceivable::class);
        $accountReceivable->load([
            'entity',
            'sale.saleDetails.productVariant.product',
            'payments.paymentMethod',
            'payments.user',
        ]);
        $company = Company::first();

        $pdf = Pdf::loadView('admin.accounts_receivable.report', [
            'ar' => $accountReceivable,
            'company' => $company,
        ])->setPaper('letter');

        return $pdf->download('cuenta_por_cobrar_' . $accountReceivable->id . '_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildAccountsReceivableQuery(Request $request)
    {
        $query = AccountReceivable::with([
            'entity',
            'sale.saleDetails.productVariant.product',
            'sale.paymentMethod',
            'payments.paymentMethod',
            'payments.user',
        ])
            ->whereHas('sale', function ($q) {
                $q->where('is_credit', true);
            });

        // Búsqueda general por cliente, ID de cuenta o ID de venta
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('entity', function ($qe) use ($search) {
                        $qe->whereRaw("TRIM(CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) LIKE ?", ['%' . $search . '%'])
                            ->orWhere('short_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('sale', function ($qs) use ($search) {
                        $qs->where('id', $search);
                    });
            });
        }

        // Filtros
        if ($entityId = $request->input('entity_id')) {
            $query->where('entity_id', $entityId);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($saleId = $request->input('sale_id')) {
            $query->where('sale_id', $saleId);
        }
        if ($from = $request->input('from')) {
            $query->whereHas('sale', function ($q) use ($from) {
                $q->whereDate('sale_date', '>=', $from);
            });
        }
        if ($to = $request->input('to')) {
            $query->whereHas('sale', function ($q) use ($to) {
                $q->whereDate('sale_date', '<=', $to);
            });
        }
        // Filtros por saldo
        if (!is_null($request->input('min_balance'))) {
            $query->whereRaw('(amount_due - amount_paid) >= ?', [(float) $request->input('min_balance')]);
        }
        if (!is_null($request->input('max_balance'))) {
            $query->whereRaw('(amount_due - amount_paid) <= ?', [(float) $request->input('max_balance')]);
        }

        return $query;
    }
}
