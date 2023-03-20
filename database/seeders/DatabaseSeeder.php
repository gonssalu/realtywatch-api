<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public static string $seedType = 'small';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('-----------------------------------------------');
        $this->command->info('Starting database seeder');
        $this->command->info('-----------------------------------------------');
/*
        DatabaseSeeder::$seedType = $this->command->choice('What type of seed do you want to run?', ['small', 'large'], 0);

        if ($this->shouldWipeRecords()) {
            $this->truncateAllTables();
        }

        $this->call(UserSeeder::class);

        */

        // $this->call(AdministrativeDivisonSeeder::class);
        $this->callWith(PropertySeeder::class, ['user' => User::first()]);
        // TODO: Testing Property Factory
        // for ($i = 0; $i < 20; $i++) {
        //     $user = resolve(PropertyFactory::class)->make();
        //     dump($user->toArray());
        // }

        $this->command->info('-----------------------------------------------');
        $this->command->info('Database seeder finished');
        $this->command->info('-----------------------------------------------');
    }

    private function shouldWipeRecords(): bool
    {
        return $this->command->confirm('Do you want to wipe all the records first?', true);
    }

    private function truncateAllTables(): void
    {
        $this->command->info('Wiping all records from the database');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $table_array = reset($table);
            DB::table($table_array)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('All records have been wiped');
    }
}
