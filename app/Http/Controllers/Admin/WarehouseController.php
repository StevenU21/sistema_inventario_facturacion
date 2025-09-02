<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WarehouseController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Warehouse::class);
        $warehouses = Warehouse::latest()->paginate(10);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function show(Warehouse $warehouse)
    {
        $this->authorize('view', $warehouse);
        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function create()
    {
        $this->authorize('create', Warehouse::class);
        return view('admin.warehouses.create');
    }

    public function store(WarehouseRequest $request)
    {
        $this->authorize('create', Warehouse::class);
        Warehouse::create($request->validated());
        return redirect()->route('warehouses.index')->with('success', 'Warehouse creado correctamente');
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        $warehouse->update($request->validated());
        return redirect()->route('warehouses.index')->with('updated', 'Warehouse actualizado correctamente');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('destroy', $warehouse);
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('deleted', 'Warehouse eliminado correctamente');
    }
}
