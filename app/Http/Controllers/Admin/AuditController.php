<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index()
    {
        $activities = Activity::latest()->paginate(10);

        foreach ($activities as $activity) {
            $changes = json_decode($activity->changes(), true);
            $old = $changes['old'] ?? [];
            $attributes = $changes['attributes'] ?? [];

            // Solo mostrar los campos que cambiaron
            $diffKeys = array_unique(array_merge(
                array_keys(array_diff_assoc($old, $attributes)),
                array_keys(array_diff_assoc($attributes, $old))
            ));

            $oldFiltered = array_intersect_key($old, array_flip($diffKeys));
            $attributesFiltered = array_intersect_key($attributes, array_flip($diffKeys));

            $activity->old = $this->arrayToString($oldFiltered);
            $activity->new = $this->arrayToString($attributesFiltered);
        }

        return view('admin.audits.index', compact('activities'));
    }

    public function arrayToString($array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[] = $this->arrayToString($value);
            } else {
                $result[] = "$key: $value";
            }
        }
        return implode(', ', $result);
    }
}
