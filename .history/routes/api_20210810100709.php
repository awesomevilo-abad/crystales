<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\ReferrenceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Methods\MasterlistMethod;

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

//     return true;
// });

Route::post('/login', [UserController::class, 'login']);
// Protected Routes

Route::middleware('auth:sanctum')->get('/authenticated', function (Request $request) {
    return $request->user();
});


Route::group(['middleware'=>'auth:sanctum'],function(){

    //MASTERLIST GENERIC METHOD
    Route::post('masterlist/restore',function (Request $request){
        MasterlistMethod::restore($request['table,$id);
    });

    // USER
    Route::get('users/username-validation', [UserController::class, 'username_validation']);
    Route::get('users/id-validation', [UserController::class, 'id_validation']);
    Route::resource('users', UserController::class);
    Route::post('users/archive/{id}', [UserController::class, 'archive']);
    Route::post('users/search/', [UserController::class, 'search']);
    Route::post('users/change-password/{id}', [UserController::class, 'change_password']);
    Route::post('/logout', [UserController::class, 'logout']);

    // CATEGORY
    Route::get('categories/all/', [CategoryController::class, 'categories']);
    Route::resource('categories', CategoryController::class);
    Route::post('categories/archive/{id}', [CategoryController::class, 'archive']);
    Route::post('categories/search/', [CategoryController::class, 'search']);

    // DOCUMENTS
    Route::get('documents/all/', [DocumentController::class, 'documents']);
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

    // BANK
    Route::resource('banks', BankController::class);
    Route::post('banks/archive/{id}', [BankController::class, 'archive']);
    Route::post('banks/search/', [BankController::class, 'search']);

    // SUPPLIER TYPE
    Route::get('supplier-types/all/', [SupplierTypeController::class, 'all']);
    Route::resource('supplier-types', SupplierTypeController::class);
    Route::post('supplier-types/archive/{id}', [SupplierTypeController::class, 'archive']);
    Route::post('supplier-types/search/', [SupplierTypeController::class, 'search']);

    // REFERRENCE
    Route::get('referrences/all/', [ReferrenceController::class, 'all']);
    Route::resource('referrences', ReferrenceController::class);
    Route::post('referrences/archive/{id}', [ReferrenceController::class, 'archive']);
    Route::post('referrences/search/', [ReferrenceController::class, 'search']);

    // SUPPLIER
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/archive/{id}', [SupplierController::class, 'archive']);
    Route::post('suppliers/search/', [SupplierController::class, 'search']);

    // TRANSACTION
    Route::resource('transactions/', TransactionController::class);
    Route::get('transactions/status_group/',[TransactionController::class,'status_group']);



});



