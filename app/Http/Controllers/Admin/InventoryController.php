<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Classes\InventoryMovementManager;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoriesExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Inventory::class);
        $perPage = request('per_page', 10);
        $inventories = Inventory::with(['product', 'warehouse'])->latest()->paginate($perPage);
        $products = Product::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', compact('inventories', 'products', 'warehouses'));
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $query = Inventory::with(['product', 'warehouse']);
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        $perPage = $request->input('per_page', 10);
        $inventories = $query->latest()->paginate($perPage)->appends($request->all());
        $products = Product::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', compact('inventories', 'products', 'warehouses'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $productId = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $stock = $request->input('stock');
        $minStock = $request->input('min_stock');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Inventory::with(['product', 'warehouse']);
        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }
        if (!empty($warehouseId)) {
            $query->where('warehouse_id', $warehouseId);
        }
        if (!empty($stock)) {
            $query->where('stock', $stock);
        }
        if (!empty($minStock)) {
            $query->where('min_stock', $minStock);
        }
        if (!empty($search)) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        $allowedSorts = ['id', 'product_id', 'warehouse_id', 'stock', 'min_stock', 'purchase_price', 'sale_price', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $timestamp = now()->format('Ymd_His');
        $filename = "inventarios_filtrados_{$timestamp}.xlsx";
        return Excel::download(new InventoriesExport($query), $filename);
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
