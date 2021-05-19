<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReasonController;
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
Route::post('categories/search/', [CategoryController::class, 'search']);

// DOCUMENTS
Route::resource('documents', DocumentController::class);
Route::post('documents/archive/{id}', [DocumentController::class, 'archive']);
Route::post('documents/search/', [DocumentController::class, 'search']);

// COMPANY
Route::resource('companies', CompanyController::class);
Route::post('companies/archive/{id}', [CompanyController::class, 'archive']);
Route::post('companies/search/', [CompanyController::class, 'search']);

// REASON
Route::resource('reasons', ReasonController::class);
Route::post('reasons/archive/{id}', [ReasonController::class, 'archive']);
Route::post('reasons/search/', [ReasonController::class, 'search']);
// REASON
Route::resource('reasons', ReasonController::class);
Route::post('reasons/archive/{id}', [ReasonController::class, 'archive']);
Route::post('reasons/search/', [ReasonController::class, 'search']);

// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
