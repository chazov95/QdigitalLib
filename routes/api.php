<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBooksController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;

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
/*Route::middleware("api.auth")->get('/user/{id}',function (Request $request, $id) {
    $user =  User::find($id);
    if (!$user) {
        return response('не найден пользователь',404);
    }
    return $user;
});*/
Route::post("/login", function (Request $request) {
    $login = $request->get('login');
    $password = $request->get('password');
    $user = DB::table("users")->where("email", "=", $login)->where("password", "=", md5($password))->first();

    return response(["token" => $user->remember_token,
        "userId" => $user->id]);
});

Route::post("/register", function (Request $request) {
    $name = trim(htmlspecialchars($request->get('name')));
    $email = trim(htmlspecialchars($request->get('email')));
    $password = trim(htmlspecialchars($request->get('password')));
    $confirmPassword = trim(htmlspecialchars($request->get('confirmPassword')));

    if ('' === $password || '' === $confirmPassword || '' === $name || '' === $email) {
        return response(["message" => "не заполнено одно из полей",
            "success" => false]);
    }

    if ($password !== $confirmPassword) {
        return response(["message" => "пароли не совпадают",
            "success" => false]);
    }

    $user = User::create(
        [
            "name" => $name,
            "email" => $email,
            "password" => md5($password),
            "remember_token" => Uuid::uuid4()->toString()
        ]);

    return response(["message" => "пользователь зарегистриован",
        "success" => true]);
});
Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => '/book/', 'middleware' => 'api.auth'], function () {
    Route::post('/add', [BookController::class, 'addBook']);
    Route::post('/makeFavorite/{bookId}', [UserBooksController::class, 'makeFavorite']);
    Route::get('/search', [BookController::class, 'search']);
    Route::get('/show', [UserBooksController::class, 'showBookList']);
    Route::get('/getInfo/{bookId}', [UserBooksController::class, 'getInfo']);
    Route::get('/deleteBook/{bookId}', [UserBooksController::class, 'deleteBook']);
});

Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => '/books '/*, 'middleware'=>'auth'*/], function () {
    Route::get('/show', [UserBooksController::class, 'showBookList']);
});
