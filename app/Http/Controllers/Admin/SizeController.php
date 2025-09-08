<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\Services\ModelSearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SizeController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Size::class);
        $perPage = request('per_page', 10);
        $sizes = Size::latest()->paginate($perPage);
        return view('admin.sizes.index', compact('sizes'));
    }

    public function search(ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Size::class);
        $params = request()->all();
        $sizes = $searchService->search(
            Size::class,
            $params,
            ['name', 'description']
        );
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        $this->authorize('create', Size::class);
        return view('admin.sizes.create');
    }

    public function store(SizeRequest $request)
    {
        Size::create($request->validated());
        return redirect()->route('sizes.index')->with('success', 'Tamaño creado correctamente.');
    }

    public function show(Size $size)
    {
        $this->authorize('view', $size);
        return view('admin.sizes.show', compact('size'));
    }

    public function edit(Size $size)
    {
        $this->authorize('update', $size);
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(SizeRequest $request, Size $size)
    {
        $size->update($request->validated());
        return redirect()->route('sizes.index')->with('updated', 'Tamaño actualizado correctamente.');
    }

    public function destroy(Size $size)
    {
        $this->authorize('destroy', $size);
        $size->delete();
        return redirect()->route('sizes.index')->with('deleted', 'Tamaño eliminado correctamente.');
    }
}
