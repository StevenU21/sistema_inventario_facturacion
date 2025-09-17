<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Quotation;
use App\Models\Entity;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);
        $term = trim((string) $request->input('q', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(20, $limit));

        $entityIds = Quotation::query()->pluck('entity_id')->unique()->values();
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
