<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WarehousesExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WarehouseController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Warehouse::class);
        $perPage = request('per_page', 10);
        $warehouses = Warehouse::latest()->paginate($perPage);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Warehouse::class);
        $query = Warehouse::query();
        // Búsqueda por nombre, dirección, descripción
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        // Filtro estado
        if ($request->filled('is_active')) {
            $isActive = $request->boolean('is_active');
            $query->where('is_active', $isActive);
        }

        // Orden
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'name', 'address', 'description', 'is_active', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);
        $warehouses = $query->paginate($perPage)->appends($request->all());
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Warehouse::class);
        $query = Warehouse::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        if ($request->filled('is_active')) {
            $isActive = $request->boolean('is_active');
            $query->where('is_active', $isActive);
        }

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'name', 'address', 'description', 'is_active', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "almacenes_{$timestamp}.xlsx";
        return Excel::download(new WarehousesExport($query), $filename);
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
        if ($warehouse->is_active) {
            $warehouse->is_active = false;
            $warehouse->save();
            return redirect()->route('warehouses.index')->with('updated', 'Almacén desactivado correctamente');
        } else {
            $this->authorize('update', $warehouse);
            $warehouse->is_active = true;
            $warehouse->save();
            return redirect()->route('warehouses.index')->with('deleted', 'Almacén reactivado correctamente.');
        }
    }
}
