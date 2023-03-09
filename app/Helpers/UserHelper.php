<?php

namespace App\Helpers;

use App\Models\User;
use Hash;
use Illuminate\Auth\Events\Registered;
use Storage;

class UserHelper
{
    public static function createAccessToken($authUser)
    {
        return $authUser->createToken('authToken', $authUser->scopes())->accessToken;
    }
}
