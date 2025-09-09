<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
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
        $inventories = Inventory::with(['productVariant.product', 'warehouse'])->latest()->paginate($perPage);
        // Obtener los IDs de variantes que ya están en inventario
        $variantIdsInInventory = Inventory::pluck('product_variant_id')->toArray();
        // Solo variantes cuyo producto está disponible y que no estén en inventario
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })
            ->whereNotIn('id', $variantIdsInInventory)
            ->with(['product', 'color', 'size'])
            ->get()
            ->mapWithKeys(function ($variant) {
                $label = $variant->product->name;
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color)
                    $label .= ' / ' . $variant->color->name;
                if ($variant->size)
                    $label .= ' / ' . $variant->size->name;
                return [$variant->id => $label];
            });
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', [
            'inventories' => $inventories,
            'variants' => $variants,
            'warehouses' => $warehouses
        ]);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $query = Inventory::with(['productVariant.product', 'warehouse']);
        // Filtros
        if ($request->filled('product_variant_id')) {
            $query->where('product_variant_id', $request->input('product_variant_id'));
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->input('warehouse_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('productVariant.product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        // Ordenamiento desde los <th>
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = [
            'id',
            'product_variant_id',
            'warehouse_id',
            'stock',
            'min_stock',
            'purchase_price',
            'sale_price',
            'created_at',
            'updated_at'
        ];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);
        $inventories = $query->paginate($perPage)->appends($request->all());
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })
            ->with(['product', 'color', 'size'])
            ->get()
            ->mapWithKeys(function ($variant) {
                $label = $variant->product->name;
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color)
                    $label .= ' / ' . $variant->color->name;
                if ($variant->size)
                    $label .= ' / ' . $variant->size->name;
                return [$variant->id => $label];
            });
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.index', [
            'inventories' => $inventories,
            'variants' => $variants,
            'warehouses' => $warehouses
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);
        $variantId = $request->input('product_variant_id');
        $warehouseId = $request->input('warehouse_id');
        $stock = $request->input('stock');
        $minStock = $request->input('min_stock');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Inventory::with(['productVariant.product', 'warehouse']);
        if (!empty($variantId)) {
            $query->where('product_variant_id', $variantId);
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
            $query->whereHas('productVariant.product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        $allowedSorts = ['id', 'product_variant_id', 'warehouse_id', 'stock', 'min_stock', 'purchase_price', 'sale_price', 'created_at'];
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
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })
            ->with(['product', 'color', 'size'])
            ->get()
            ->mapWithKeys(function ($variant) {
                $label = $variant->product->name;
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color)
                    $label .= ' / ' . $variant->color->name;
                if ($variant->size)
                    $label .= ' / ' . $variant->size->name;
                return [$variant->id => $label];
            });
        $warehouses = Warehouse::pluck('name', 'id');
        return view('admin.inventories.create', [
            'variants' => $variants,
            'warehouses' => $warehouses
        ]);
    }

    public function store(InventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);
        $data = $request->validated();

        \DB::beginTransaction();
        try {

            $inventory = Inventory::create($data);

            $isFirst = !InventoryMovement::whereHas('inventory', function ($q) use ($inventory) {
                $q->where('product_variant_id', $inventory->product_variant_id);
            })->exists();

            $inventory->inventoryMovements()->create([
                'type' => 'in',
                'quantity' => $inventory->stock,
                'unit_price' => $inventory->purchase_price ?? 0,
                'total_price' => ($inventory->purchase_price ?? 0) * $inventory->stock,
                'reference' => $isFirst ? 'Inventario Inicial' : 'Registro inicial',
                'notes' => $isFirst ? 'Primer registro de inventario para este producto' : 'Creación de inventario',
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
        $variants = ProductVariant::whereHas('product', function ($q) {
            $q->where('status', 'available');
        })
            ->with(['product', 'color', 'size'])
            ->get()
            ->mapWithKeys(function ($variant) {
                $label = $variant->product->name;
                if ($variant->name)
                    $label .= ' / ' . $variant->name;
                if ($variant->color)
                    $label .= ' / ' . $variant->color->name;
                if ($variant->size)
                    $label .= ' / ' . $variant->size->name;
                return [$variant->id => $label];
            });
        $warehouses = Warehouse::pluck('name', 'id');
        // Movimientos recientes
        $movements = $inventory->inventoryMovements()->latest()->take(10)->get();
        return view('admin.inventories.edit', [
            'inventory' => $inventory,
            'variants' => $variants,
            'warehouses' => $warehouses,
            'movements' => $movements
        ]);
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
