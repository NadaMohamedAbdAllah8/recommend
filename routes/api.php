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
    Route::post('login', 'AuthController@login');

    // protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        // Route::get('me', 'AuthController@me');

        Route::post('logout', 'AuthController@logout')->name('logout');

    });
});