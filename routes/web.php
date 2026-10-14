<?php

use App\Http\Controllers\OrdersController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrdersController::class, 'index'])->name('customer.index');
Route::post('/checkout', [OrdersController::class, 'store'])->name('customer.checkout');

Route::get('/dashboard', [OrdersController::class, 'adminDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/dashboard', [OrdersController::class,'adminDashboard'])->name('admin.dashboard');
    Route::patch('/admin/orders/{id}/update-status', [OrdersController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    Route::resource('admin/events', EventsController::class);
});

require __DIR__.'/auth.php';
