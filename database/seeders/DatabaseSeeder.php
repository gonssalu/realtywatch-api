<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('-----------------------------------------------');
        $this->command->info('Starting database seeder');
        $this->command->info('-----------------------------------------------');

        $seedType = $this->command->choice('What type of seed do you want to run?', ['small', 'large'], 0);

        $this->call(AdministrativeDivisionSeeder::class);

        $this->callWith(UserSeeder::class, ['seedType' => $seedType]);
        $this->callWith(PropertySeeder::class, ['user' => User::first()]);

        // DO NOT RUN // $this->call(MediaSeeder::class);

        $this->command->info('-----------------------------------------------');
        $this->command->info('Database seeder finished');
        $this->command->info('-----------------------------------------------');
    }
}
