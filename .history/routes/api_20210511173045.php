<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
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
Route::post('/login', [UserController::class, 'login']);

Route::resource('users', UserController::class);
Route::resource('categories', CategoryController::class);
Route::resource('documents', DocumentController::class);
// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // USER
    Route::post('/archive/{id}', [UserController::class, 'archive']);
    Route::post('/search/', [UserController::class, 'search']);
    Route::post('/change-password/{id}', [UserController::class, 'change_password']);
    Route::post('/logout', [UserController::class, 'logout']);

    // CATEGORY
    Route::post('/archive/{id}', [UserController::class, 'archive']);

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
