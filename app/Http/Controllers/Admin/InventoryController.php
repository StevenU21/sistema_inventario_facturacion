<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
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
        // ...
    }

    public function store(InventoryRequest $request)
    {
        $this->authorize('create', Inventory::class);
        // ...
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);
        // ...
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        // ...
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        // ...
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);
        // ...
    }
}
