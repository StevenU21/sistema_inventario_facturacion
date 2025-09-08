<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
use App\Services\ModelSearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ColorController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Color::class);
        $perPage = request('per_page', 10);
        $colors = Color::latest()->paginate($perPage);
        return view('admin.colors.index', compact('colors'));
    }

    public function search(ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Color::class);
        $params = request()->all();
        $colors = $searchService->search(
            Color::class,
            $params,
            ['name', 'hex_code']
        );
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        $this->authorize('create', Color::class);
        return view('admin.colors.create');
    }

    public function store(ColorRequest $request)
    {
        Color::create($request->validated());
        return redirect()->route('colors.index')->with('success', 'Color creado correctamente.');
    }

    public function show(Color $color)
    {
        $this->authorize('view', $color);
        return view('admin.colors.show', compact('color'));
    }

    public function edit(Color $color)
    {
        $this->authorize('update', $color);
        return view('admin.colors.edit', compact('color'));
    }

    public function update(ColorRequest $request, Color $color)
    {
        $color->update($request->validated());
        return redirect()->route('colors.index')->with('updated', 'Color actualizado correctamente.');
    }

    public function destroy(Color $color)
    {
        $this->authorize('destroy', $color);
        $color->delete();
        return redirect()->route('colors.index')->with('deleted', 'Color eliminado correctamente.');
    }
}