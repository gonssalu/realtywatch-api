<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run($seedType): void
    {
        $numUsers = $seedType === 'small' ? 1 : 5;

        // Create a regular user
        User::factory($numUsers)->create();

        // Create a user with no profile picture
        User::factory($numUsers)->create(['photo_url' => null]);

        // Create a user with a blocked account
        User::factory($numUsers)->create(['blocked' => true]);

        // Create a soft deleted user
        User::factory($numUsers)->trashed()->create();
    }
}
