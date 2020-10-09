<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::prefix('v1')->group(function () {

    // 商品瀏覽
    Route::get('getproduct', 'App\Http\Controllers\api\MerchandiseUserController@getProduct');

    // 測試綠界
    Route::post('/callback', 'App\Http\Controllers\api\MerchandiseUserController@callback');
    Route::get('/success', 'App\Http\Controllers\api\MerchandiseUserController@redirectFromECpay');

    Route::prefix('user')->group(function () {
        // 註冊
        Route::post('register', 'App\Http\Controllers\api\UserController@register');
        // 登入
        Route::post('login', 'App\Http\Controllers\api\UserController@login');
        // 取得資訊
        Route::middleware('auth:sanctum')->get('/getuser', 'App\Http\Controllers\api\UserController@getUser');

        Route::group(['middleware' => ['auth:sanctum']], function () {

            Route::prefix('products')->group(function () {
                // 指定商品
                Route::post('/uploadimg', 'App\Http\Controllers\api\MerchandiseUserController@uploadImg');
            });

            Route::prefix('cart')->group(function () {
                // 新增購物車
                Route::post('/', 'App\Http\Controllers\api\MerchandiseUserController@createCart');
                // 刪除購物車
                Route::delete('/', 'App\Http\Controllers\api\MerchandiseUserController@deleteCart');
                // 更新購物車
                Route::put('/', 'App\Http\Controllers\api\MerchandiseUserController@updateCart');
                // 取得購物車
                Route::get('/', 'App\Http\Controllers\api\MerchandiseUserController@getCart');
            });

            // 訂單處理
            Route::prefix('order')->group(function () {

                // 新增訂單
                Route::post('/', 'App\Http\Controllers\api\MerchandiseUserController@createOrder');
                
            });          

        });
    });

    //管理者
    Route::prefix('admin')->group(function () {

        // 指定商品
        // Route::post('/uploadimg', 'App\Http\Controllers\api\AdminsController@uploadImg');
        // 取得資訊
        Route::group(['middleware' => ['auth:sanctum', 'auth.admin']], function () {
            
            Route::prefix('products')->group(function () {
                // 新增產品
                Route::post('/', 'App\Http\Controllers\api\MerchandiseController@createProduct');
                // 刪除產品
                Route::delete('/', 'App\Http\Controllers\api\MerchandiseController@deleteProduct');
                // 更新產品
                Route::put('/', 'App\Http\Controllers\api\MerchandiseController@updateProduct');
                // 取得產品
                Route::get('/', 'App\Http\Controllers\api\MerchandiseController@getProduct');
                // 上傳圖片
                Route::post('/uploadimg', 'App\Http\Controllers\api\MerchandiseController@uploadImg');
            });

            Route::prefix('cart')->group(function () {
                // 新增購物車
                Route::post('/', 'App\Http\Controllers\api\MerchandiseController@createCart');
                // 刪除購物車
                Route::delete('/', 'App\Http\Controllers\api\MerchandiseController@deleteCart');
                // 更新購物車
                Route::put('/', 'App\Http\Controllers\api\MerchandiseController@updateCart');
                // 取得購物車
                Route::get('/', 'App\Http\Controllers\api\MerchandiseController@getCart');
            });

            // 模擬類
            Route::prefix('order')->group(function () {

                // 取得訂單
                Route::get('/', 'App\Http\Controllers\api\MerchandiseController@getOrder');

                // 模擬結帳
                Route::post('/pay', 'App\Http\Controllers\api\MerchandiseController@Simulationpay');
            });

            

        });
    });
});
