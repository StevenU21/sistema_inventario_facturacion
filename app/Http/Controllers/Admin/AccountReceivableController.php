<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\AccountReceivable;
use App\Models\Entity;
use App\Models\Company;
use App\Exports\AccountReceivablesExport;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountReceivableController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountReceivable::class);

        $query = $this->buildAccountsReceivableQuery($request);
        // Filtrar por defecto solo estados 'pending' o 'partially_paid' si no se especifica 'status'
        if (!$request->has('status')) {
            $query->whereIn('status', ['pending', 'partially_paid']);
        }
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

        $methods = PaymentMethod::pluck('name', 'id');

        return view('admin.accounts_receivable.index', compact('accounts', 'entities', 'statuses', 'methods'));
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

        // Build filename including client name
        $clientName = trim(($accountReceivable->entity?->first_name ?? '') . ' ' . ($accountReceivable->entity?->last_name ?? ''));
        $clientSlug = $clientName ? str_replace(' ', '_', $clientName) : 'cliente';
        $filename = sprintf(
            'cuenta_por_cobrar_%s_%d_%s.pdf',
            $clientSlug,
            $accountReceivable->id,
            now()->format('Ymd_His')
        );
        return $pdf->download($filename);
    }

    // Endpoint para autocompletar en índices (cuentas por cobrar)
    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', AccountReceivable::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        // Solo clientes con cuentas por cobrar
        $entityIds = AccountReceivable::query()
            ->whereHas('entity')
            ->pluck('entity_id')
            ->unique()
            ->values();

        $entitiesQuery = Entity::query()->whereIn('id', $entityIds);

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

        $suggestions = $entities->map(function ($e) {
            $name = trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')) ?: ($e->short_name ?? '');
            return [
                'id' => $e->id,
                'text' => $name,
            ];
        });

        return response()->json([
            'data' => $suggestions,
        ]);
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
                        $qe->whereRaw("TRIM(COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')) LIKE ?", ['%' . $search . '%'])
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

    /**
     * Registrar un pago para una cuenta por cobrar específica.
     */
    public function storePayment(Request $request, AccountReceivable $accountReceivable)
    {
        // Reutilizamos política de creación de pagos
        $this->authorize('create', Payment::class);

        // No permitir pagos a cuentas ya saldadas
        if ($accountReceivable->status === 'paid') {
            return back()->with('error', __('Esta cuenta ya está pagada.'));
        }

        $amount_due = round($accountReceivable->amount_due ?? 0, 2);
        $amount_paid = round($accountReceivable->amount_paid ?? 0, 2);
        $remaining = $amount_due - $amount_paid;

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ]);

        $paymentAmount = round((float) $validated['amount'], 2);

        // No permitir pagos que excedan el monto total de la cuenta
        if ($paymentAmount > $remaining) {
            return back()->withInput()->with('error', __('El monto no puede exceder el saldo restante de C$ :remaining', ['remaining' => number_format($remaining, 2)]));
        }

        // No permitir pagos si la cuenta ya está pagada
        if ($accountReceivable->status === 'paid' || $remaining <= 0) {
            return back()->with('error', __('Esta cuenta ya está pagada.'));
        }

        DB::transaction(function () use ($validated, $accountReceivable, $amount_due, $amount_paid, $paymentAmount) {
            $payment = new Payment();
            $payment->amount = $paymentAmount;
            $payment->payment_date = now()->toDateString();
            $payment->account_receivable_id = $accountReceivable->id;
            $payment->payment_method_id = (int) $validated['payment_method_id'];
            $payment->entity_id = $accountReceivable->entity_id;
            $payment->user_id = Auth::id();
            $payment->save();

            // Actualizar acumulado y estado
            $newPaid = round($amount_paid + $paymentAmount, 2);
            $accountReceivable->amount_paid = $newPaid;
            if ($newPaid >= $amount_due) {
                $accountReceivable->status = 'paid';
            } elseif ($newPaid > 0) {
                $accountReceivable->status = 'partially_paid';
            }
            $accountReceivable->save();
        });

        return back()->with('success', __('Pago registrado correctamente.'));
    }
}
