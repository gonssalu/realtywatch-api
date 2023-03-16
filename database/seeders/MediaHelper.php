<?php

namespace Database\Seeders;

use Http;
use Storage;

class MediaHelper
{
    private static function SearchPhoto($args)
    {
        $response = Http::withHeaders([
            'Authorization' => env('PEXELS_API_KEY'),
        ])->get("https://api.pexels.com/v1/search$args");

        return $response->json()['photos'];
    }

    public static function GetUserPhoto($name): string
    {
        // Download the photo and save it to storage
        $photoContents = file_get_contents(config('factory.photo.api.url') . $name);
        $photoPath = 'public/users/' . uniqid() . '.jpg';
        Storage::put($photoPath, $photoContents);
        return Storage::url($photoPath);
    }
}
