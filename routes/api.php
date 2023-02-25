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

// Route::namespace ('App\Http\Controllers\Admin')->prefix('api')
//     ->middleware(['api:admin'])->group(function () {

Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin'],
    function () {

        Route::post('login', 'AuthController@login');

        // protected routes
        Route::group(['middleware' => ['auth:api', 'admin']], function () {
            // Route::get('me', 'AuthController@me');

            Route::post('logout', 'AuthController@logout')->name('logout');

            Route::prefix('products')->group(function () {
                Route:: as ('Product.')->group(function () {
                    Route::get('/', 'ProductController@index')->name('index');
                    Route::post('store', 'ProductController@store')->name('store');
                    Route::post('store', 'ProductController@store')->name('store');
                    Route::post('update/{id}', 'ProductController@update')->name('update');
                    Route::post('destroy/{id}', 'ProductController@destroy')->name('destroy');
                });
            });

        });

    });

// issue new tokens
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// users routes

Route::group(['prefix' => 'user', 'namespace' => 'App\Http\Controllers\User'], function () {
    Route::post('register', 'AuthController@register')->name('register');

    Route::post('login', 'AuthController@login');

    // protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        // Route::get('me', 'AuthController@me');

        Route::post('logout', 'AuthController@logout')->name('logout');

        Route::apiResource('products', 'ProductController')->except(['store', 'update', 'destroy']);

        // Route::prefix('Products')->group(function () {
        //     Route:: as ('Product.')->group(function () {
        //         Route::get('/', 'ProductController@index')->name('index');
        //         Route::get('show/{id}', 'ProductController@show')->name('show');

        //     });
        // });

    });
});
