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

// Protected Routes
// Route::group(['middleware' => ['auth:sanctum']], function () {

// USER
Route::resource('users', UserController::class);
Route::post('users/archive/{id}', [UserController::class, 'archive']);
Route::post('users/search/', [UserController::class, 'search']);
Route::post('users/change-password/{id}', [UserController::class, 'change_password']);
Route::post('/logout', [UserController::class, 'logout']);

// CATEGORY
Route::resource('categories', CategoryController::class);
Route::post('categories/archive/{id}', [CategoryController::class, 'archive']);

// DOCUMENTS
Route::resource('documents', DocumentController::class);

// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
