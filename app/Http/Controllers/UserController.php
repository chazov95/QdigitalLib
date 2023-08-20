<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');
        $user = DB::table("users")->where("email", "=", $login)->where("password", "=", md5($password))->first();

        return response([
            "token" => $user->remember_token,
            "userId" => $user->id
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $name = trim(htmlspecialchars($request->get('name')));
        $email = trim(htmlspecialchars($request->get('email')));
        $password = trim(htmlspecialchars($request->get('password')));
        $confirmPassword = trim(htmlspecialchars($request->get('confirmPassword')));

        if ('' === $password || '' === $confirmPassword || '' === $name || '' === $email) {
            return response(
                ["message" => "не заполнено одно из полей",
                "success" => false
                ]);
        }

        if ($password !== $confirmPassword) {
            return response(
                ["message" => "пароли не совпадают",
                "success" => false
                ]);
        }

        $user = User::create(
            [
                "name" => $name,
                "email" => $email,
                "password" => md5($password),
                "remember_token" => Uuid::uuid4()->toString()
            ]);

        return response(
            ["message" => "пользователь зарегистриован",
            "success" => true
            ]);
    }
}
