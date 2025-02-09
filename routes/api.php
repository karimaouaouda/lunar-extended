<?php

use App\Http\Controllers\Controller;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('fonts/create', [Controller::class, 'createFont']);
Route::get('fonts/{user}', [Controller::class, 'fetchFonts']);

Route::get('/test', function () {
    return "hi";
});


Route::controller(\App\Http\Controllers\DesignController::class)
    ->group(function () {
        Route::prefix('designs')
            ->group(function () {
                Route::get('/{design}', 'show');
                Route::post('/create', 'store');
                Route::post('/{id}/update', 'update');
                Route::post('/{id}/delete', 'destroy');
            });

            Route::get('/{user}/designs', 'fetch');
    });

Route::controller(\App\Http\Controllers\AlbumController::class)
    ->prefix('albums')
    ->group(function () {
        Route::get('/{albumId}', 'fetchAlbum');
        Route::post('/{albumId}/push-picture', 'pushPicture');
    });

Route::controller(\App\Http\Controllers\ProductController::class)
    ->group(function () {

        Route::get('/collections/{name}/products', 'collection_products');

        Route::prefix('products')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{product_id}', 'show');
            });
    });
