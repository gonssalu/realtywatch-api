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

        // Get the first two users
        $users = User::query()->take(2)->get();

        $this->callWith(PropertySeeder::class, ['user' => $users[0], 'num_props' => $seedType === 'small' ? 3 : 100]);
        $this->callWith(ListTagSeeder::class, ['user' => $users[0], 'qty' => $seedType === 'small' ? [1, [1, 2], 2, [1, 2]] : [7, [8, 16], 15, [1, 5]]]);

        $this->callWith(PropertySeeder::class, ['user' => $users[1], 'num_props' => $seedType === 'small' ? 2 : 10]);
        $this->callWith(ListTagSeeder::class, ['user' => $users[1], 'qty' => [1, [1, 2], 2, [1, 2]]]);

        // DO NOT RUN // $this->call(MediaSeeder::class);

        $this->command->info('-----------------------------------------------');
        $this->command->info('Database seeder finished');
        $this->command->info('-----------------------------------------------');
    }
}
