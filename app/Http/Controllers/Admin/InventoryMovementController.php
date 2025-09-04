<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryMovementController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', InventoryMovement::class);
        $inventoryMovements = InventoryMovement::with(['inventory.product', 'user'])->latest()->paginate(10);
        return view('admin.inventory_movements.index', compact('inventoryMovements'));
    }
}
