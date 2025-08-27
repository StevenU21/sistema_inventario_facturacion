<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\InactiveUserController;
use App\Http\Controllers\Admin\MunicipalityController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UnitMeasureController;
use App\Http\Controllers\Admin\UserController;
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

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('companies', CompanyController::class)->except(['destroy']);
    Route::resource('unit_measures', UnitMeasureController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('municipalities', MunicipalityController::class);

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/forms', function () {
        return view('pages/forms');
    })->name('forms');

    Route::get('/cards', function () {
        return view('pages/cards');
    })->name('cards');

    Route::get('/charts', function () {
        return view('pages/charts');
    })->name('charts');

    Route::get('/buttons', function () {
        return view('pages/buttons');
    })->name('buttons');

    Route::get('/modals', function () {
        return view('pages/modals');
    })->name('modals');

    Route::get('/tables', function () {
        return view('pages/tables');
    })->name('tables');
});

require __DIR__ . '/auth.php';
