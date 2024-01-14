<?php

use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RazorpayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

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

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class,'register']);
// Route::post('/create', [TaskController::class,'store']);
// Route::get('/tasks', [TaskController::class,'index']);
// Route::get('/user/{id}/tasks', [TaskController::class,'getUserTasks']);
// Route::delete('/task/{id}', [TaskController::class,'destroy']);
// Route::patch('/task/{id}', [TaskController::class,'updateTask']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});


Route::group(['middleware' => ['auth:sanctum']], function () {
    // all routes that require authentication inside this group
    



});

// Routes outside the group (do not require authentication)

Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', [AuthController::class, 'me']);
Route::get('/export', [TaskController::class, 'export']);
Route::post('/upload', [TaskController::class, 'upload']);
Route::post('/upload', [ProductController::class, 'upload']);


Route::post('/razorpay/capture-payment', [RazorpayController::class, 'capturePayment']);
Route::post('/razorpay/payment-failure', 'RazorpayController@handlePaymentFailure');
Route::get('/razorpay/payment-failure-data', 'RazorpayController@getPaymentFailureData');





   
    Route::patch('/product-update/{id}', [ProductController::class, 'updateProduct']);
    Route::get(`/product/{id}`, [ProductController::class, 'show']);
    Route::get('/products', [BuyerController::class, 'index']);
    Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart']);
    Route::delete('/remove-from-cart/{id}', [CartController::class, 'removeFromCart']);
    Route::get('/cart-items', [CartController::class, 'fetchCartItems']);
    Route::patch('/product-stock-status', [ProductController::class,'updateStockStatus']);
    Route::post('/save-order-data', [ProductController::class,'saveOrderData']);
    Route::get('/seller/sold-products ', [ProductController::class,'fetchSoldProducts']);
    Route::get('/seller/purchases-history ', [ProductController::class,'purchaseHistory']);
    Route::get('/purchases', [BuyerController::class,'orderHistory']);
    Route::get('/wallet/details ', [BuyerController::class,'walletDetails']);
    Route::resource('/product', ProductController::class);
    Route::patch('/product-status', [ProductController::class,'updateActiveStatus']);
    Route::get('/product-detail', [ProductController::class,'show']);
    Route::delete('/delete-product', [ProductController::class,'destroy']);
