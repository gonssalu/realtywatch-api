<?php

namespace Database\Seeders;

use App\Models\AdministrativeDivision;
use Illuminate\Database\Seeder;

class AdministrativeDivisonSeeder extends Seeder
{

    private function getAllFreguesias(): array
    {
        $filename = storage_path('app/data/freguesias_portugal.csv');
        $delimiter = ';';
        if (!file_exists($filename) || !is_readable($filename)) {
            return []; //TODO
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, null, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting Administrative Divison Seeder...');
        $freguesias = $this->getAllFreguesias();

        if (empty($freguesias)) {

            $this->command->warn('No administrative divisions were created.');
            return;
        }

        $distritos = [];
        $concelhos = [];

        $count = 0;
        foreach ($freguesias as $freg) {
        }

        $this->command->info($count . ' divisions created.');
        $this->command->info('Administrative Division Seeder finished. ');
    }
}
