<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\EntityController;
use App\Http\Controllers\Admin\InactiveUserController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InventoryMovementController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\UnitMeasureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Admin Routes
    Route::get('users/inactive', [InactiveUserController::class, 'index'])->name('users.inactive');
    Route::post('users/inactive/{id}/reactivate', [InactiveUserController::class, 'reactivate'])->name('inactive-users.reactivate');
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');
    Route::resource('users', UserController::class);

    // User Permissions
    Route::get('users/{user}/permissions/edit', [PermissionController::class, 'edit'])->name('users.permissions.edit');
    Route::post('users/{user}/permissions/assign', [PermissionController::class, 'assignPermission'])->name('users.permissions.assign');
    Route::post('users/{user}/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('users.permissions.revoke');

    // Audit Logs
    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('audits/export', [AuditController::class, 'export'])->name('audits.export');

    // Backups
    Route::get('admin/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('admin/backups/restore', [BackupController::class, 'restore'])->name('backups.restore');

    Route::get('categories/search', [CategoryController::class, 'search'])->name('categories.search');
    Route::resource('categories', CategoryController::class);
    Route::get('brands/search', [BrandController::class, 'search'])->name('brands.search');
    Route::resource('brands', BrandController::class);
    Route::resource('companies', CompanyController::class)->except(['destroy']);
    Route::get('unit_measures/search', [UnitMeasureController::class, 'search'])->name('unit_measures.search');
    Route::resource('unit_measures', UnitMeasureController::class);
    Route::get('payment_methods/search', [PaymentMethodController::class, 'search'])->name('payment_methods.search');
    Route::resource('payment_methods', PaymentMethodController::class);
    Route::get('taxes/search', [TaxController::class, 'search'])->name('taxes.search');
    Route::resource('taxes', TaxController::class);
    Route::resource('entities', EntityController::class);
    Route::resource('products', ProductController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('inventory_movements', InventoryMovementController::class);

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
