<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => '/book/api'/*, 'middleware'=>'auth'*/], function () {
    Route::post('/add', [BookController::class, 'addBook']);
    Route::post('/makeFavorite', [UserBooksController::class, 'makeFavorite']);
    Route::get('/search', [BookController::class, 'search']);
    Route::get('/show', [UserBooksController::class, 'showBookList']);
    Route::get('/getInfo', [UserBooksController::class, 'getInfo']);
    Route::get('/deleteBook', [UserBooksController::class, 'deleteBook']);
});

Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => '/books '/*, 'middleware'=>'auth'*/], function () {
    Route::get('/show', [UserBooksController::class, 'showBookList']);
});

require __DIR__ . '/auth.php';
