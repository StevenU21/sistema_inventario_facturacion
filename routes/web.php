<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::get('/categories', function () {
    return view('categories/index');
})->name('categories.index');

Route::get('/categories/create', function () {
    return view('categories/create');
})->name('categories.create');

Route::get('/categories/show', function () {
    return view('categories/show');
})->name('categories.show');

Route::middleware('auth')->prefix('/profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('destroy');
});

// Admin Routes
Route::resource('users', UserController::class);

// User Permissions
Route::get('users/{user}/permissions/edit', [PermissionController::class, 'edit'])->name('users.permissions.edit');
Route::post('users/{user}/permissions/assign', [PermissionController::class, 'assignPermission'])->name('users.permissions.assign');
Route::post('users/{user}/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('users.permissions.revoke');


require __DIR__ . '/auth.php';
