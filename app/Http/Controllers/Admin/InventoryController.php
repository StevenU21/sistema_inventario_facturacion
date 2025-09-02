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
                $reference = $request->input('reference');
                $notes = $request->input('notes');
                $unit_price = $request->input('unit_price', 0);

                if ($type === 'transfer') {
                    $quantity = (int) $request->input('quantity');
                    $destWarehouseId = $request->input('destination_warehouse_id');
                    if (!$destWarehouseId || !$quantity) {
                        \DB::rollBack();
                        return back()->withErrors(['destination_warehouse_id' => 'Select destination warehouse and quantity.']);
                    }
                    if ($inventory->warehouse_id == $destWarehouseId) {
                        \DB::rollBack();
                        return back()->withErrors(['destination_warehouse_id' => 'Cannot transfer to the same warehouse.']);
                    }
                    if ($inventory->stock < $quantity) {
                        \DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient stock to transfer.']);
                    }
                    // Find destination inventory for product and warehouse
                    $destInventory = Inventory::where('product_id', $inventory->product_id)
                        ->where('warehouse_id', $destWarehouseId)
                        ->first();
                    if ($destInventory && $destInventory->id == $inventory->id) {
                        \DB::rollBack();
                        return back()->withErrors(['destination_warehouse_id' => 'Product already exists in selected warehouse.']);
                    }
                    // Debit current stock
                    $inventory->stock -= $quantity;
                    $inventory->save();
                    // Register transfer movement (out) in origin warehouse
                    $inventory->inventoryMovements()->create([
                        'type' => 'transfer',
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'total_price' => $unit_price * $quantity,
                        'reference' => $reference,
                        'notes' => $notes,
                        'user_id' => auth()->id(),
                    ]);
                    // If destination inventory does not exist, create it
                    if (!$destInventory) {
                        $destInventory = Inventory::create([
                            'product_id' => $inventory->product_id,
                            'warehouse_id' => $destWarehouseId,
                            'stock' => 0,
                            'min_stock' => $inventory->min_stock,
                            'purchase_price' => $inventory->purchase_price,
                            'sale_price' => $inventory->sale_price,
                        ]);
                    }
                    $destInventory->stock += $quantity;
                    $destInventory->save();
                    // Register transfer movement (in) in destination warehouse
                    $destInventory->inventoryMovements()->create([
                        'type' => 'in',
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'total_price' => $unit_price * $quantity,
                        'reference' => $reference,
                        'notes' => 'Transfer from origin warehouse',
                        'user_id' => auth()->id(),
                    ]);
                } elseif ($type === 'adjustment' || $type === 'in') {
                    $newStock = $request->input('stock');
                    $newMinStock = $request->input('min_stock');
                    $newUnitPrice = $request->input('unit_price');
                    $newSalePrice = $request->input('sale_price');
                    $quantity = $type === 'adjustment' ? $newStock : $request->input('quantity');
                    // Update inventory
                    $inventory->stock = $newStock;
                    $inventory->min_stock = $newMinStock;
                    $inventory->purchase_price = $newUnitPrice;
                    $inventory->sale_price = $newSalePrice;
                    $inventory->save();
                    $inventory->inventoryMovements()->create([
                        'type' => $type,
                        'quantity' => $quantity,
                        'unit_price' => $newUnitPrice,
                        'total_price' => $newUnitPrice * $quantity,
                        'reference' => $reference,
                        'notes' => $notes,
                        'user_id' => auth()->id(),
                    ]);
                } elseif ($type === 'out') {
                    $quantity = (int) $request->input('quantity');
                    if ($inventory->stock < $quantity) {
                        \DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient stock for output.']);
                    }
                    $inventory->stock -= $quantity;
                    $inventory->save();
                    $inventory->inventoryMovements()->create([
                        'type' => 'out',
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'total_price' => $unit_price * $quantity,
                        'reference' => $reference,
                        'notes' => $notes,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            \DB::commit();
            return redirect()->route('inventories.index')->with('success', 'Inventario actualizado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el inventario: ' . $e->getMessage()]);
        }
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('destroy', $inventory);
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Inventario eliminado correctamente.');
    }
}
