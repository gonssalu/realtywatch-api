<?php

namespace Database\Seeders\helpers;

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

    /**
     * Retrieve user photo by name and store it to disk.
     *
     * @param  string  $name  The name to generate the user photo.
     *
     * @return string The URL of the stored user photo.
     */
    public static function GetUserPhoto($name): string
    {
        $photoContents = file_get_contents(config('factory.media.api.url') . urlencode($name));
        $photoPath = 'public/users/' . uniqid() . '.png';
        Storage::put($photoPath, $photoContents);

        return Storage::url($photoPath);
    }
}
