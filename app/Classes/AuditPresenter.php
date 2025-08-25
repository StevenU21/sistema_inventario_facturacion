<?php

namespace App\Classes;

use Spatie\Activitylog\Models\Activity;

class AuditPresenter
{
    /**
     * Devuelve un array con los datos formateados para mostrar o exportar.
     */
    public static function present(Activity $activity)
    {
        $changes = json_decode($activity->changes(), true);
        $old = $changes['old'] ?? [];
        $attributes = $changes['attributes'] ?? [];

        $diffKeys = array_unique(array_merge(
            array_keys(array_diff_assoc($old, $attributes)),
            array_keys(array_diff_assoc($attributes, $old))
        ));

        $oldFiltered = array_intersect_key($old, array_flip($diffKeys));
        $attributesFiltered = array_intersect_key($attributes, array_flip($diffKeys));

        return [
            'ID' => $activity->id,
            'Fecha' => $activity->created_at->format('d/m/Y H:i:s'),
            'Usuario' => $activity->causer ? (($activity->causer->first_name ?? '') . ' ' . ($activity->causer->last_name ?? '')) : '-',
            'Evento' => $activity->event ?? '-',
            'Modelo' => class_basename($activity->subject_type) ?? '-',
            'ID Modelo' => $activity->subject_id ?? '-',
            'Antes' => self::arrayToString($oldFiltered),
            'Después' => self::arrayToString($attributesFiltered),
        ];
    }

    public static function arrayToString($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[] = self::arrayToString($value);
            } else {
                $result[] = "$key: $value";
            }
        }
        return implode(', ', $result);
    }
}
