<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'v1'], function () {
    Route::post('register', 'API\V1\UserController@register');
    Route::post('login', 'API\V1\UserController@loginCustomer');

    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', 'API\V1\UserController@loginAdmin');
    });
    // Route::apiResource('categories', 'API\V1\CategoriesController');

    Route::group(
        ['middleware' => ['auth:api', 'role:admin'], 'prefix' => 'admin'],
        function () {
            Route::apiResource('categories', 'API\V1\CategoriesController');
            Route::get(
                'parent/categories',
                'API\V1\CategoriesController@indexParent'
            )->name('categories.parent');
            Route::get(
                'categoriesAll',
                'API\V1\CategoriesController@indexAll'
            )->name('categories.index.all');
            Route::apiResource('products', 'API\V1\ProductController');
        }
    );

    Route::group(['middleware' => ['auth:api', 'role:customer']], function () {
        Route::get('/abcd', function () {
            return 'hello';
        });
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
