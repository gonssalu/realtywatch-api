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
            return [];
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, null, $delimiter)) !== false) {
                if (!$header) {
                    $header = array_map(function ($value) {
                        return ltrim($value, "\xef\xbb\xbf");
                    }, $row);
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

            // Create Distrito if it doesn't exist yet
            $nomeDis = trim($freg['distrito']);
            if (!array_key_exists($nomeDis, $distritos)) {

                $distritos[$nomeDis] =
                    AdministrativeDivision::create([
                        'name' => $nomeDis,
                        'level' => '1',
                    ]);;

                // Log district creation
                $this->command->info('  Seeding ' . $nomeDis . ' district...');
            }

            // Create Concelho if it doesn't exist yet
            $nomeCon = trim($freg['concelho']);
            $conKey = $nomeDis . $nomeCon;
            if (!array_key_exists($conKey, $concelhos)) {
                if ($count > 0)
                    $this->command->info('          ' . $count . ' freguesias created.');
                $concelhos[$conKey] =
                    AdministrativeDivision::create([
                        'name' => $nomeCon,
                        'level' => '2',
                        'parent_id' => $distritos[$nomeDis]->id
                    ]);

                // Log district creation
                $this->command->info('      [+] Concelho ' . $nomeCon);
                $count = 0;
            }

            // Create Freguesia
            AdministrativeDivision::create([
                'name' => trim($freg['freguesia']),
                'level' => '3',
                'parent_id' => $concelhos[$conKey]->id
            ]);
            $count++;
        }

        $this->command->info(sizeof($distritos) . ' distritos created.');
        $this->command->info(sizeof($concelhos) . ' concelhos created.');
        $this->command->info(sizeof($freguesias) . ' freguesias created.');
        $this->command->info('Administrative Division Seeder finished. ');
    }
}
