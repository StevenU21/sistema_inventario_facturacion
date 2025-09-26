<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use App\Exports\AuditExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ModelSearchService;
use App\Services\AuditService;

class AuditController extends Controller
{
    use AuthorizesRequests;
    public function index(AuditService $auditService)
    {
        $this->authorize('viewAny', Activity::class);
        $query = $auditService->baseQuery()->latest();
        $activities = $auditService->paginateAndPresent($query, 10);
        $allCausers = $auditService->causers();
        $allModels = $auditService->models();
        $modelTranslations = $auditService->modelTranslations();
        return view('admin.audits.index', compact('activities', 'allCausers', 'allModels', 'modelTranslations'));
    }

    public function search(Request $request, ModelSearchService $searchService, AuditService $auditService)
    {
        $this->authorize('viewAny', Activity::class);
        $filters = [
            'causer_id' => $request->input('causer_id'),
            'event' => $request->input('event'),
            'model' => $request->input('model'),
            'range' => $request->input('range'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        $perPage = (int) $request->input('per_page', 10);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query = $auditService->baseQuery();
        $auditService->applyFilters($query, $filters);
        $auditService->applySorting($query, $sort, $direction);

        $activities = $auditService->paginateAndPresent($query, $perPage, $request->all());
        $allCausers = $auditService->causers();
        $allModels = $auditService->models();
        $modelTranslations = $auditService->modelTranslations();
        return view('admin.audits.index', compact('activities', 'allCausers', 'allModels', 'modelTranslations'));
    }

    public function export(Request $request, AuditService $auditService)
    {
        $this->authorize('export', Activity::class);
        $filters = [
            'causer_id' => $request->input('causer_id'),
            'event' => $request->input('event'),
            'model' => $request->input('model'),
            'range' => $request->input('range'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = $auditService->buildForExport($filters, $sort, $direction);
        $timestamp = now()->format('Ymd_His');
        $filename = "auditoria_filtrada_{$timestamp}.xlsx";
        return Excel::download(new AuditExport($query), $filename);
    }
}
