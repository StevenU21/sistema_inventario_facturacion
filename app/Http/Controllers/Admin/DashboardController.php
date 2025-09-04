<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::count();
        $entities = Entity::where("is_client", 1)->count();
        $users = User::count();
        $inventoryTotal = Inventory::sum('stock');
        $movementsToday = InventoryMovement::whereDate('created_at', now()->toDateString())->count();

        return view('dashboard', compact('products', 'entities', 'users', 'inventoryTotal', 'movementsToday'));
    }
}