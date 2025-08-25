<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Models\Activity;
use \App\Classes\AuditPresenter;
use Illuminate\Http\Request;
use App\Exports\AuditExport;
use Maatwebsite\Excel\Facades\Excel;

class AuditController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Activity::class);
        $activities = Activity::latest()->paginate(10);

        foreach ($activities as $activity) {
            $presented = AuditPresenter::present($activity);
            $activity->old = $presented['Antes'];
            $activity->new = $presented['Después'];
            $activity->evento_es = $presented['Evento'];
            $activity->modelo_es = $presented['Modelo'];
        }

        return view('admin.audits.index', compact('activities'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Activity::class);

        $range = $request->input('range', 'completo');
        $query = Activity::query();

        if ($range === 'hoy') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($range === 'semana') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'mes') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "auditoria_{$range}_{$timestamp}.xlsx";
        return Excel::download(new AuditExport($query), $filename);
    }
}
