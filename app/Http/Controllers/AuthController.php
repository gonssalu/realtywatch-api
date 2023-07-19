<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid login credentials'], 401);
        }

        if ($user->blocked) {
            return response(['message' => 'User is blocked'], 403);
        }

        $accessToken = $user->myCreateToken($request->device_name);

        return response(['message' => 'Login was successful', 'user' => new UserResource($user), 'access_token' => $accessToken]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return response(['message' => 'Token revoked']);
    }

    public function register(RegisterUserRequest $request)
    {
        $newUser = $request->validated();

        $newUser['password'] = Hash::make($newUser['password']);
        $newUser['blocked'] = false;

        $regUser = User::create($newUser);

        $accessToken = $regUser->myCreateToken($request->device_name);

        return response([
            'message' => 'User was successfuly registered',
            'user' => new UserResource($regUser),
            'access_token' => $accessToken,
        ]);
    }
}
