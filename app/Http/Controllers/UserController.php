<?php

namespace App\Http\Controllers;

use App\Helpers\StorageLocation;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Hash;
use Illuminate\Http\Request;
use Storage;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();
        $newUser = $request->validated();
        $deleteUserPhoto = false;

        //Update profile picture
        if ($request->hasFile('photo')) {
            $newUser['photo_url'] = basename($request->file('photo')->store(StorageLocation::USER_PHOTOS));
            unset($newUser['photo']);

            $deleteUserPhoto = true;
        } elseif ($request->has('remove_photo') && $request->remove_photo) {
            $newUser['photo_url'] = null;
            $deleteUserPhoto = true;
        }

        //TODO: Resend email verification?

        //Delete photo when true
        if (
            $deleteUserPhoto &&
            $user->photo_url
        ) {
            Storage::delete(StorageLocation::USER_PHOTOS . '/' . $user->photo_url);
        }

        $user->update($newUser);

        return response(['message' => 'User updated', 'user' => new UserResource($user)]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed|between:6,128',
        ]);

        //Check if old password is correct
        if (!Hash::check($request->old_password, $request->user()->password)) {
            return response(['message' => 'Current password is incorrect'], 401);
        }

        $user = $request->user();

        $user->password = Hash::make($request->new_password);
        $user->save();

        $tokenName = $user->currentAccessToken()->name;

        //Revoke tokens on pass change
        foreach ($user->tokens as $token) {
            $token->delete();
        }

        $newToken = $user->myCreateToken($tokenName);

        return response(['message' => 'Password changed', 'access_token' => $newToken]);
    }
}
