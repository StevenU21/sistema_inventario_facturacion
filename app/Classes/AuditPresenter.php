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

        // Traducción de atributos por modelo
        $attributeTranslations = [
            'User' => [
                'first_name' => 'Nombre',
                'last_name' => 'Apellido',
                'email' => 'Correo',
                'password' => 'Contraseña',
                'is_active' => 'Activo',
            ],
            'Profile' => [
                'avatar' => 'Avatar',
                'phone' => 'Teléfono',
                'identity_card' => 'Cédula',
                'gender' => 'Género',
                'address' => 'Dirección',
                'user_id' => 'Usuario',
            ],
        ];

        $modelKey = class_basename($activity->subject_type);
        $attrMap = $attributeTranslations[$modelKey] ?? [];

        $oldFiltered = self::translateKeys($oldFiltered, $attrMap);
        $attributesFiltered = self::translateKeys($attributesFiltered, $attrMap);

        // Traducción de eventos
        $eventMap = [
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
        ];
        $evento = $activity->event ?? '-';
        $evento = $eventMap[$evento] ?? $evento;

        // Traducción de modelos
        $modelMap = [
            'User' => 'Usuario',
            'Profile' => 'Perfil',
        ];
        $modelo = class_basename($activity->subject_type) ?? '-';
        $modelo = $modelMap[$modelo] ?? $modelo;

        return [
            'ID' => $activity->id,
            'Fecha' => $activity->created_at->format('d/m/Y H:i:s'),
            'Usuario' => $activity->causer ? (($activity->causer->first_name ?? '') . ' ' . ($activity->causer->last_name ?? '')) : '-',
            'Evento' => $evento,
            'Modelo' => $modelo,
            'ID Modelo' => $activity->subject_id ?? '-',
            'Antes' => self::arrayToString($oldFiltered),
            'Después' => self::arrayToString($attributesFiltered),
        ];
    }

    /**
     * Convierte un array en una cadena legible.
     */
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

    /**
     * Traduce las claves de un array según un mapa de traducción.
     */
    public static function translateKeys(array $array, array $map): array
    {
        $translated = [];
        foreach ($array as $key => $value) {
            $translatedKey = $map[$key] ?? $key;
            $translated[$translatedKey] = $value;
        }
        return $translated;
    }
}
