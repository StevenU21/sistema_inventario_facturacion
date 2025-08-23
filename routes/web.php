<?php

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
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
require __DIR__ . '/auth.php';
