<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\StockChallanController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix'=>'/auth'],function(){
    Route::post('/register',[UserController::class,'createUser']);
    Route::post('/login',[UserController::class,'loginUser']);
    Route::get('auth-failed',function(){
        return response()->json([
            'status'=>false,
            'message'=>'unauthorized'
        ],401);
    })->name('api-auth-fail');
});

Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::resource('brand',BrandController::class);
    Route::resource('category',CategoryController::class);
    Route::resource('unit',UnitController::class);
    Route::resource('product',ProductController::class);
    Route::resource('department',DepartmentController::class);
    Route::get('stock-challan', [StockChallanController::class,'index']);
    Route::post('stock-challan', [StockChallanController::class,'updateStock']);
    Route::get('sales',[SalesController::class,'index']);
    Route::post('sales',[SalesController::class,'generateSales']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

