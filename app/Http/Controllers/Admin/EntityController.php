<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntityRequest;
use App\Models\Entity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EntityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Entity::class);
        $entities = Entity::latest()->paginate(10);
        return view('admin.entities.index', compact('entities'));
    }

    public function create()
    {
        $this->authorize('create', Entity::class);
        return view('admin.entities.create');
    }

    public function store(EntityRequest $request)
    {
        Entity::create($request->validated());
        return redirect()->route('entities.index')->with('success', 'Entidad creada correctamente.');
    }

    public function show(Entity $entity)
    {
        $this->authorize('view', $entity);
        return view('admin.entities.show', compact('entity'));
    }

    public function edit(Entity $entity)
    {
        $this->authorize('update', $entity);
        return view('admin.entities.edit', compact('entity'));
    }

    public function update(EntityRequest $request, Entity $entity)
    {
        $entity->update($request->validated());
        return redirect()->route('entities.index')->with('success', 'Entidad actualizada correctamente.');
    }

    public function destroy(Entity $entity)
    {
        $this->authorize('destroy', $entity);
        $entity->delete();
        return redirect()->route('entities.index')->with('success', 'Entidad eliminada correctamente.');
    }
}
