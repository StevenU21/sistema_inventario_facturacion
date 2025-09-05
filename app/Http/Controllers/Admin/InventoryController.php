<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Classes\InventoryMovementManager;
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

        \DB::beginTransaction();
        try {
            $inventory = Inventory::create($data);

            $inventory->inventoryMovements()->create([
                'type' => 'in',
                'quantity' => $inventory->stock,
                'unit_price' => $inventory->purchase_price ?? 0,
                'total_price' => ($inventory->purchase_price ?? 0) * $inventory->stock,
                'reference' => 'Registro inicial',
                'notes' => 'Creación de inventario',
                'user_id' => auth()->id(),
            ]);

            \DB::commit();
            return redirect()->route('inventories.index')->with('success', 'Inventario creado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el inventario: ' . $e->getMessage()]);
        }
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
        // Movimientos recientes
        $movements = $inventory->inventoryMovements()->latest()->take(10)->get();
        return view('admin.inventories.edit', compact('inventory', 'products', 'warehouses', 'movements'));
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $data = $request->validated();

        \DB::beginTransaction();
        try {
            $inventory->update($data);

            if ($request->has('movement_type') && $request->input('movement_type')) {
                $type = $request->input('movement_type');
                $result = null;
                if ($type === 'transfer') {
                    $result = InventoryMovementManager::transfer($inventory, $request);
                } else if ($type === 'adjustment') {
                    $result = InventoryMovementManager::adjust($inventory, $request);
                }
                if (is_array($result) && isset($result['error'])) {
                    \DB::rollBack();
                    return back()->withErrors($result['error']);
                }
            }

            \DB::commit();
            return redirect()->route('inventories.index')->with('updated', 'Inventario actualizado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el inventario: ' . $e->getMessage()]);
        }
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('destroy', $inventory);
        $inventory->delete();
        return redirect()->route('inventories.index')->with('deleted', 'Inventario eliminado correctamente.');
    }
}
