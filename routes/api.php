<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('user/')->group(
    function () {
        Route::post('register', [UserController::class, 'register']);
        Route::post('login', [UserController::class, 'login']);
        Route::get('inventories', [InventoryController::class, 'listInventory'])->middleware('auth:api');
        // cart
        Route::prefix('cart')->group(function () {
            Route::post('add', [CartController::class, 'addToCart']);
            Route::get('list', [CartController::class,'listCart']);
            Route::delete('delete/{cart_id}', [CartController::class, 'deleteFromCart']);
            Route::patch('update/{cart_id}', [CartController::class, 'updateCart']);
        });
    }
);
// admin route
Route::prefix('admin/')->group(
    function () {
        Route::post('register', [AdminController::class, 'register']);
        Route::post('login', [AdminController::class, 'login']);
        Route::prefix('inventory')->group(function () {
            Route::post('add', [InventoryController::class, 'addInventory']);
            Route::get('list', [InventoryController::class, 'listInventory']);
            Route::get('view/{id}', [InventoryController::class,'viewInventory']);
            Route::delete('delete/{id}', [InventoryController::class, 'deleteInventory']);
            Route::patch('update/{id}', [InventoryController::class, 'updateInventory']);
        });
    }
);
