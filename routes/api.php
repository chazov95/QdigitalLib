<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBooksController;
use App\Http\Controllers\UserController;
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

Route::post("/login", [UserController::class, 'login']);
Route::post("/register", [UserController::class, 'register']);

Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => '/book/', 'middleware' => 'api.auth'], function () {
    Route::post('/add', [BookController::class, 'addBook']);
    Route::post('/makeFavorite/{bookId}', [UserBooksController::class, 'makeFavorite']);
    Route::get('/search', [BookController::class, 'search']);
    Route::get('/show', [UserBooksController::class, 'showBookList']);
    Route::get('/getInfo/{bookId}', [UserBooksController::class, 'getInfo']);
    Route::get('/deleteBook/{bookId}', [UserBooksController::class, 'deleteBook']);
});
