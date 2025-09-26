<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Classes\AuditPresenter;
use App\Classes\AuditTranslation;
use Illuminate\Support\Collection;

class AuditService
{
    /**
     * Construye el query base con relaciones.
     */
    public function baseQuery(): Builder
    {
        return Activity::query()->with(['causer', 'subject']);
    }

    /**
     * Aplica filtros dinámicos.
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['causer_id'])) {
            $query->where('causer_id', $filters['causer_id']);
        }
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }
        if (!empty($filters['model'])) {
            $query->where('subject_type', $filters['model']);
        }
        if (!empty($filters['range'])) {
            $range = $filters['range'];
            if ($range === 'hoy') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($range === 'semana') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($range === 'mes') {
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
        } else {
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
        return $query;
    }

    /**
     * Aplica ordenamiento seguro.
     */
    public function applySorting(Builder $query, string $sort, string $direction): Builder
    {
        $allowedSorts = ['id', 'causer_id', 'event', 'subject_type', 'subject_id', 'created_at'];
        if (in_array($sort, $allowedSorts, true)) {
            return $query->orderBy($sort, $direction);
        }
        return $query->latest();
    }

    /**
     * Pagina actividades y las formatea.
     */
    public function paginateAndPresent(Builder $query, int $perPage = 10, array $append = []): LengthAwarePaginator
    {
        $paginator = $query->paginate($perPage)->appends($append);
        $this->presentCollection($paginator->getCollection());
        return $paginator;
    }

    /**
     * Presenta/transforma una colección de actividades in-place.
     */
    public function presentCollection(Collection $activities): void
    {
        $modelTranslations = AuditTranslation::modelMap(); // si se necesita en el futuro
        foreach ($activities as $activity) {
            $presented = AuditPresenter::present($activity);
            $activity->old = $presented['Antes'];
            $activity->new = $presented['Después'];
            $activity->evento_es = $presented['Evento'];
            $activity->modelo_es = $presented['Registro'];
            $activity->model_display = $this->resolveModelDisplay($activity);
            // Nombre completo del usuario (causer) precalculado para evitar lógica en la vista
            $activity->causer_name = $activity->causer
                ? trim(($activity->causer->first_name ?? '') . ' ' . ($activity->causer->last_name ?? ''))
                : '-';
        }
    }

    /**
     * Obtiene lista de causers únicos.
     */
    public function causers(): Collection
    {
        return Activity::with('causer')->get()->pluck('causer')->filter()->unique('id')->values();
    }

    /**
     * Obtiene lista de modelos únicos (subject_type).
     */
    public function models(): Collection
    {
        return Activity::select('subject_type')->distinct()->pluck('subject_type');
    }

    /**
     * Traducciones de modelos.
     */
    public function modelTranslations(): array
    {
        return AuditTranslation::modelMap();
    }

    /**
     * Export helper: retorna builder listo (sin paginar) formateo se hará en exportador si aplica.
     */
    public function buildForExport(array $filters, string $sort, string $direction): Builder
    {
        $query = $this->baseQuery();
        $this->applyFilters($query, $filters);
        $this->applySorting($query, $sort, $direction);
        return $query;
    }

    private function resolveModelDisplay(Activity $activity): string
    {
        if ($activity->subject) {
            $subject = $activity->subject;
            if (isset($subject->name)) {
                return $subject->name;
            }
            if (isset($subject->title)) {
                return $subject->title;
            }
            if (isset($subject->first_name) || isset($subject->last_name)) {
                return trim(($subject->first_name ?? '') . ' ' . ($subject->last_name ?? '')) ?: '-';
            }
            return (string) ($activity->subject_id ?? '-');
        }
        return (string) ($activity->subject_id ?? '-');
    }
}
