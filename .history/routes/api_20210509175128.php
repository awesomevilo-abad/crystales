<?php

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

//  Public Routes

// // Protected Routes
// Route::group(['middleware' => ['auth:sanctum']], function () {
//     Route::post('/products', [ProductController::class, 'store']);
//     Route::put('/products/{id}', [ProductController::class, 'update']);
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//     Route::post('/logout', [UserController::class, 'logout']);

// });

Route::resource('users', UserController::class);

Route::post('/archive/{id}', [UserController::class, 'archive']);
Route::post('/search/', [UserController::class, 'search']);
Route::post('/change-password/{id}', [UserController::class, 'change_password']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
