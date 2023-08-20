<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\DB;

class AuthenticateApi extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        $token = $request->bearerToken();
        $userId = $request->header('USER_ID');

        $user = DB::table("users")
            ->where("remember_token","=",$token)->first();

        if (!$user instanceof \stdClass) {
            $this->unauthenticated($request, $guards);
        }

        if ((int)$user->id === (int)$userId) {
            return;
        }

        $this->unauthenticated($request, $guards);
    }
}
