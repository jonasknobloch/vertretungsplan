<?php

namespace App\Http\Controllers;

use App\UserAuthToken;

class UserAuthTokenController extends Controller {

    public function setAuthTokenByUserId($userId) {

        // TODO: validate user ID
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        return UserAuthToken::create([
            'user_id' => $userId,
            'auth_token' => $token
        ]);
    }

    public function checkForValidAuthTokenAndReturnUserOnSuccess()
    {
        if (isset($_COOKIE['schedule_auth_token'])) {
            $tokenCollection = UserAuthToken::where('auth_token', $_COOKIE['schedule_auth_token'])->get();
            if ($tokenCollection->isNotEmpty()) {
                return $tokenCollection->first()->user;
            }
        }

        return false;
    }
}