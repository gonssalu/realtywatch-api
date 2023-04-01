<?php

namespace App\Http\Controllers;

use App\Helpers\StorageLocation;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
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
        } else if ($request->has('remove_photo') && $request->remove_photo) {
            $newUser['photo_url'] = null;
            $deleteUserPhoto = true;
        }

        //Delete photo when true
        if (
            $deleteUserPhoto &&
            $user->photo_url
        )
            Storage::delete(StorageLocation::USER_PHOTOS . '/' . $user->photo_url);

        $user->update($newUser);

        return response(['message' => 'User updated', 'user' => new UserResource($user)]);
    }
}
