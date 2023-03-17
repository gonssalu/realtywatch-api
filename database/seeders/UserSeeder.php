<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): User
    {
        // Create a regular user
        $user = User::factory()->create();

        // Create a user with no profile picture
        User::factory()->create(['photo_url' => null]);

        // Create a user with a blocked account
        User::factory()->create(['blocked' => true]);

        // Create a soft deleted user
        User::factory()->trashed()->create();

        return $user;
    }
}
