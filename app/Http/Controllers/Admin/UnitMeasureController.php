<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitMeasure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UnitMeasureController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', UnitMeasure::class);
        $unitMeasures = UnitMeasure::latest()->paginate(10);
        return view('admin.unit_measures.index', compact('unitMeasures'));
    }
}
