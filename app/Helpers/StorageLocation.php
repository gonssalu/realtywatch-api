<?php

namespace App\Helpers;

class StorageLocation
{
    public const PROPERTY_MEDIA = 'public/properties';

    public const AGENCY_LOGOS = 'public/agencies';

    public const USER_PHOTOS = 'public/users';

    public static function GenerateFileName($id): string
    {
        return $id . '_' . md5(uniqid() . time());
    }
}
