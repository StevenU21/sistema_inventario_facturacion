<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EntityController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InventoryMovementController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\UnitMeasureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\KardexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Profile Routes
    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Admin Routes
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);

    // User Permissions
    Route::get('users/{user}/permissions/edit', [PermissionController::class, 'edit'])->name('users.permissions.edit');
    Route::post('users/{user}/permissions/assign', [PermissionController::class, 'assignPermission'])->name('users.permissions.assign');
    Route::post('users/{user}/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('users.permissions.revoke');

    // Audit Logs
    Route::get('audits/search', [AuditController::class, 'search'])->name('audits.search');
    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('audits/export', [AuditController::class, 'export'])->name('audits.export');

    // Backups
    Route::get('admin/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('admin/backups/restore', [BackupController::class, 'restore'])->name('backups.restore');

    // Categories
    Route::get('categories/search', [CategoryController::class, 'search'])->name('categories.search');
    Route::resource('categories', CategoryController::class);

    // Brands
    Route::get('brands/search', [BrandController::class, 'search'])->name('brands.search');
    Route::resource('brands', BrandController::class);

    // Companies
    Route::resource('companies', CompanyController::class)->except(['destroy']);

    // Unit Measures
    Route::get('unit_measures/search', [UnitMeasureController::class, 'search'])->name('unit_measures.search');
    Route::resource('unit_measures', UnitMeasureController::class);

    // Payment Methods
    Route::get('payment_methods/search', [PaymentMethodController::class, 'search'])->name('payment_methods.search');
    Route::resource('payment_methods', PaymentMethodController::class);

    // Taxes
    Route::get('taxes/search', [TaxController::class, 'search'])->name('taxes.search');

    // Taxes
    Route::resource('taxes', TaxController::class);

    // Colors
    Route::get('colors/search', [ColorController::class, 'search'])->name('colors.search');
    Route::resource('colors', ColorController::class);

    // Sizes
    Route::get('sizes/search', [SizeController::class, 'search'])->name('sizes.search');
    Route::resource('sizes', SizeController::class);

    // Entities
    Route::get('entities/search', [EntityController::class, 'search'])->name('entities.search');
    Route::get('entities/export', [EntityController::class, 'export'])->name('entities.export');
    Route::resource('entities', EntityController::class);

    // Products
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class);

    // Product Variants
    Route::get('product_variants/search', [ProductVariantController::class, 'search'])->name('product_variants.search');
    Route::get('product_variants/export', [ProductVariantController::class, 'export'])->name('product_variants.export');
    Route::resource('product_variants', ProductVariantController::class);

    // Roles
    Route::get('roles/search', [RoleController::class, 'search'])->name('roles.search');
    Route::resource('roles', RoleController::class);

    // Warehouses
    Route::get('warehouses/search', [WarehouseController::class, 'search'])->name('warehouses.search');
    Route::get('warehouses/export', [WarehouseController::class, 'export'])->name('warehouses.export');
    Route::resource('warehouses', WarehouseController::class);

    // Inventories
    Route::get('inventories/search', [InventoryController::class, 'search'])->name('inventories.search');
    Route::get('inventories/export', [InventoryController::class, 'export'])->name('inventories.export');
    Route::resource('inventories', InventoryController::class);

    // Inventory Movements
    Route::get('inventory_movements/search', [InventoryMovementController::class, 'search'])->name('inventory_movements.search');
    Route::get('inventory_movements/export', [InventoryMovementController::class, 'export'])->name('inventory_movements.export');
    Route::resource('inventory_movements', InventoryMovementController::class);

    // Kardex
    Route::get('kardex', [KardexController::class, 'index'])->name('kardex.index');
    Route::get('kardex/export', [KardexController::class, 'exportPdf'])->name('kardex.export');

    // Purchases
    Route::get('purchases/search', [PurchaseController::class, 'search'])->name('purchases.search');
    Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
    Route::get('purchases/{purchase}/export', [PurchaseController::class, 'exportDetails'])->name('purchases.exportDetails');
    Route::resource('purchases', PurchaseController::class);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

require __DIR__ . '/auth.php';
