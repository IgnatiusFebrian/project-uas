<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncomingGoodsController;
use App\Http\Controllers\OutgoingGoodsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReturnGoodsController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data Routes
    Route::resource('items', ItemController::class);
    Route::resource('users', UserController::class);

    // Incoming Goods Routes
    Route::prefix('incoming-goods')->group(function () {
        Route::get('/', [IncomingGoodsController::class, 'index'])->name('incoming_goods.index');
        Route::get('/create', [IncomingGoodsController::class, 'create'])->name('incoming_goods.create');
        Route::post('/', [IncomingGoodsController::class, 'store'])->name('incoming_goods.store');
        Route::get('/report', [IncomingGoodsController::class, 'report'])->name('incoming_goods.report');
        Route::get('/{id}/edit', [IncomingGoodsController::class, 'edit'])->name('incoming_goods.edit');
        Route::put('/{id}', [IncomingGoodsController::class, 'update'])->name('incoming_goods.update');
    });

    // Outgoing Goods Routes
    Route::prefix('outgoing-goods')->group(function () {
        Route::get('/', [OutgoingGoodsController::class, 'index'])->name('outgoing_goods.index');
        Route::get('/create', [OutgoingGoodsController::class, 'create'])->name('outgoing_goods.create');
        Route::post('/', [OutgoingGoodsController::class, 'store'])->name('outgoing_goods.store');
        Route::get('/report', [OutgoingGoodsController::class, 'report'])->name('outgoing_goods.report');
        Route::get('/{id}/edit', [OutgoingGoodsController::class, 'edit'])->name('outgoing_goods.edit');
        Route::put('/{id}', [OutgoingGoodsController::class, 'update'])->name('outgoing_goods.update');
    });

    Route::resource('barang-keluar', BarangKeluarController::class)->only(['index', 'create', 'store']);


    // Transactions Routes (if still needed)
    Route::group(['prefix' => 'transactions', 'as' => 'transactions.'], function () {
        Route::get('/report', [TransactionController::class, 'report'])->name('report');
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // Return Goods Routes
    Route::prefix('returns')->group(function () {
        Route::get('/', [ReturnGoodsController::class, 'index'])->name('returns.index');
        Route::get('/create', [ReturnGoodsController::class, 'create'])->name('returns.create');
        Route::post('/', [ReturnGoodsController::class, 'store'])->name('returns.store');
        Route::get('/{id}/edit', [ReturnGoodsController::class, 'edit'])->name('returns.edit');
        Route::put('/{id}', [ReturnGoodsController::class, 'update'])->name('returns.update');
        Route::delete('/{id}', [ReturnGoodsController::class, 'destroy'])->name('returns.destroy');
    });
});

// Add these routes with admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/items/{item}/edit-price', 'ItemController@editPrice')->name('items.edit-price');
    Route::patch('/items/{item}/update-price', 'ItemController@updatePrice')->name('items.update-price');
});

require __DIR__.'/auth.php';
