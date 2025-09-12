<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryMovementsExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class InventoryMovementController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', InventoryMovement::class);
        $perPage = request('per_page', 10);
    $inventoryMovements = InventoryMovement::with(['inventory.productVariant.product', 'user', 'inventory.warehouse'])
            ->latest()
            ->paginate($perPage);
        $users = User::pluck('first_name', 'id');
        $products = Product::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        // Obtener colores y tallas globales
        $variants = \App\Models\ProductVariant::with(['color', 'size'])->get();
        $colors = $variants->pluck('color')->filter()->unique('id')->mapWithKeys(function ($color) {
            return [$color->id => $color->name];
        });
        $sizes = $variants->pluck('size')->filter()->unique('id')->mapWithKeys(function ($size) {
            return [$size->id => $size->name];
        });
        return view('admin.inventory_movements.index', compact('inventoryMovements', 'users', 'products', 'warehouses', 'colors', 'sizes'));
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', InventoryMovement::class);
    $query = InventoryMovement::with(['inventory.productVariant.product', 'inventory.warehouse', 'user']);

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('product_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            });
        }
        if ($request->filled('color_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('color_id', $request->input('color_id'));
            });
        }
        if ($request->filled('size_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('size_id', $request->input('size_id'));
            });
        }
        if ($request->filled('warehouse_id')) {
            $query->whereHas('inventory', function ($q) use ($request) {
                $q->where('warehouse_id', $request->input('warehouse_id'));
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('inventory.productVariant.product', function ($pq) use ($search) {
                $pq->where('name', 'like', "%$search%");
            });
        }

        // Ordenamiento
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'user_id', 'inventory_id', 'type', 'quantity', 'unit_price', 'total_price', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);
        $inventoryMovements = $query->paginate($perPage)->appends($request->all());

        $users = User::pluck('first_name', 'id');
        $products = Product::pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');
        $variants = \App\Models\ProductVariant::with(['color', 'size'])->get();
        $colors = $variants->pluck('color')->filter()->unique('id')->mapWithKeys(function ($color) {
            return [$color->id => $color->name];
        });
        $sizes = $variants->pluck('size')->filter()->unique('id')->mapWithKeys(function ($size) {
            return [$size->id => $size->name];
        });
        return view('admin.inventory_movements.index', compact('inventoryMovements', 'users', 'products', 'warehouses', 'colors', 'sizes'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', InventoryMovement::class);
    $query = InventoryMovement::with(['inventory.productVariant.product', 'inventory.warehouse', 'user']);

        // Mismos filtros que search
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('product_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            });
        }
        if ($request->filled('color_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('color_id', $request->input('color_id'));
            });
        }
        if ($request->filled('size_id')) {
            $query->whereHas('inventory.productVariant', function ($q) use ($request) {
                $q->where('size_id', $request->input('size_id'));
            });
        }
        if ($request->filled('warehouse_id')) {
            $query->whereHas('inventory', function ($q) use ($request) {
                $q->where('warehouse_id', $request->input('warehouse_id'));
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('inventory.productVariant.product', function ($pq) use ($search) {
                $pq->where('name', 'like', "%$search%");
            });
        }

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'user_id', 'inventory_id', 'type', 'quantity', 'unit_price', 'total_price', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "movimientos_inventario_{$timestamp}.xlsx";
        return Excel::download(new InventoryMovementsExport($query), $filename);
    }

    // Endpoint para autocompletar búsquedas de movimientos
    public function autocomplete(Request $request)
    {
        $this->authorize('viewAny', InventoryMovement::class);
        $term = trim((string) $request->input('q', ''));
        $limit = max(1, min(20, (int) $request->input('limit', 10)));

        // Autocomplete basado en productos, como en ProductController
        $q = \App\Models\Product::query();
        if ($term !== '') {
            $tokens = array_values(array_filter(preg_split('/\s+/', $term)));
            $driver = DB::getDriverName();
            $collation = 'utf8mb4_unicode_ci';
            $q->where(function ($qb) use ($tokens, $driver, $collation) {
                foreach ($tokens as $token) {
                    $like = "%$token%";
                    $qb->where(function ($sub) use ($like, $driver, $collation) {
                        if ($driver === 'mysql') {
                            $sub->whereRaw("name COLLATE $collation LIKE ?", [$like]);
                        } else {
                            $sub->where('name', 'like', $like);
                        }
                    });
                }
            });
        }

        $products = $q->select(['id', 'name'])
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $suggestions = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->name,
                'type' => 'producto',
            ];
        });

        return response()->json(['data' => $suggestions]);
    }
}
