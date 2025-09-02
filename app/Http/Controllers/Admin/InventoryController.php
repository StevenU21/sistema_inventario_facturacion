<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Inventory::class);
        $inventories = Inventory::with(['product', 'warehouse'])->latest()->paginate(10);
        return view('admin.inventories.index', compact('inventories'));
    }

    public function create()
    {
        $this->authorize('create', Inventory::class);
        $products = Product::where('status', 'available')->pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.create', compact('products', 'warehouses'));
    }

    public function store(InventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);
        $data = $request->validated();
        Inventory::create($data);
        return redirect()->route('inventories.index')->with('success', 'Inventario creado correctamente.');
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);
        return view('admin.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $products = Product::where('status', 'available')->pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.edit', compact('inventory', 'products', 'warehouses'));
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $data = $request->validated();
        $inventory->update($data);
        return redirect()->route('inventories.index')->with('success', 'Inventario actualizado correctamente.');
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Inventario eliminado correctamente.');
    }
}
