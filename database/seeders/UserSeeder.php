<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the User Seeder.
     *
     * @param  mixed  $seedType
     */
    public function run($seedType): void
    {
        $numUsers = $seedType === 'small' ? 1 : 5;

        $this->command->info('Creating ' . $numUsers * 4 . ' users...');

        // Create a regular user
        User::factory($numUsers + 1)->create();

        // Create a user with no profile picture
        User::factory($numUsers)->create(['photo_url' => null]);

        // Create a user with a blocked account
        User::factory($numUsers)->create(['blocked' => true]);

        // Create a soft deleted user
        User::factory($numUsers)->trashed()->create();
    }
}
