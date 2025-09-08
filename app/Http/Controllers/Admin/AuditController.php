<?php

namespace App\Http\Controllers\Admin;

use App\Classes\AuditTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Models\Activity;
use \App\Classes\AuditPresenter;
use Illuminate\Http\Request;
use App\Exports\AuditExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ModelSearchService;

class AuditController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Activity::class);
        $activities = Activity::with(['causer', 'subject'])->latest()->paginate(10);
        $allCausers = Activity::with('causer')->get()->pluck('causer')->filter()->unique('id')->values();
        $allModels = Activity::select('subject_type')->distinct()->pluck('subject_type');
        // Obtener traducciones de modelos
        $modelTranslations = AuditTranslation::modelMap();
        foreach ($activities as $activity) {
            $presented = AuditPresenter::present($activity);
            $activity->old = $presented['Antes'];
            $activity->new = $presented['Después'];
            $activity->evento_es = $presented['Evento'];
            $activity->modelo_es = $presented['Registro'];
            if ($activity->subject) {
                if (isset($activity->subject->name)) {
                    $activity->model_display = $activity->subject->name;
                } elseif (isset($activity->subject->title)) {
                    $activity->model_display = $activity->subject->title;
                } elseif (isset($activity->subject->first_name) || isset($activity->subject->last_name)) {
                    $activity->model_display = trim(($activity->subject->first_name ?? '') . ' ' . ($activity->subject->last_name ?? ''));
                } else {
                    $activity->model_display = $activity->subject_id ?? '-';
                }
            } else {
                $activity->model_display = $activity->subject_id ?? '-';
            }
        }

        return view('admin.audits.index', compact('activities', 'allCausers', 'allModels', 'modelTranslations'));
    }

    public function search(Request $request, ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Activity::class);
    $perPage = $request->input('per_page', 10);
    $causerId = $request->input('causer_id');
        $event = $request->input('event');
        $model = $request->input('model');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $range = $request->input('range');

        $query = Activity::with(['causer', 'subject']);
        if (!empty($causerId)) {
            $query->where('causer_id', $causerId);
        }
        if (!empty($event)) {
            $query->where('event', $event);
        }
        if (!empty($model)) {
            $query->where('subject_type', $model);
        }
        if (!empty($range)) {
            if ($range === 'hoy') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($range === 'semana') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($range === 'mes') {
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
        } else {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
        // Ordenamiento seguro solo por columnas válidas
        $allowedSorts = ['id', 'causer_id', 'event', 'subject_type', 'subject_id', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $activities = $query->paginate($perPage)->appends($request->all());
        $allCausers = Activity::with('causer')->get()->pluck('causer')->filter()->unique('id')->values();
        $allModels = Activity::select('subject_type')->distinct()->pluck('subject_type');
        // Obtener traducciones de modelos
        $modelTranslations = AuditTranslation::modelMap();
        foreach ($activities as $activity) {
            $presented = AuditPresenter::present($activity);
            $activity->old = $presented['Antes'];
            $activity->new = $presented['Después'];
            $activity->evento_es = $presented['Evento'];
            $activity->modelo_es = $presented['Modelo'];
            if ($activity->subject) {
                if (isset($activity->subject->name)) {
                    $activity->model_display = $activity->subject->name;
                } elseif (isset($activity->subject->title)) {
                    $activity->model_display = $activity->subject->title;
                } elseif (isset($activity->subject->first_name) || isset($activity->subject->last_name)) {
                    $activity->model_display = trim(($activity->subject->first_name ?? '') . ' ' . ($activity->subject->last_name ?? ''));
                } else {
                    $activity->model_display = $activity->subject_id ?? '-';
                }
            } else {
                $activity->model_display = $activity->subject_id ?? '-';
            }
        }

        return view('admin.audits.index', compact('activities', 'allCausers', 'allModels', 'modelTranslations'));
    }

    public function export(Request $request)
    {
        $this->authorize('export', Activity::class);

    $causerId = $request->input('causer_id');
        $event = $request->input('event');
        $model = $request->input('model');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $range = $request->input('range');

        $query = Activity::with(['causer', 'subject']);
        if (!empty($causerId)) {
            $query->where('causer_id', $causerId);
        }
        if (!empty($event)) {
            $query->where('event', $event);
        }
        if (!empty($model)) {
            $query->where('subject_type', $model);
        }
        if (!empty($range)) {
            if ($range === 'hoy') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($range === 'semana') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($range === 'mes') {
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
        } else {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
        $allowedSorts = ['id', 'causer_id', 'event', 'subject_type', 'subject_id', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "auditoria_filtrada_{$timestamp}.xlsx";
        return Excel::download(new AuditExport($query), $filename);
    }
}
