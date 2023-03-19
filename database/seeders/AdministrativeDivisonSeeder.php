<?php

namespace Database\Seeders;

use App\Models\AdministrativeDivision;
use Illuminate\Database\Seeder;
use Str;

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
            $this->command->warn('No administrative divisions were created');
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar(sizeof($freguesias));

        $distritos = [];
        $concelhos = [];

        foreach ($freguesias as $freg) {

            // Create Distrito if it doesn't exist yet
            $nomeDis = trim($freg['distrito']);
            if (!array_key_exists($nomeDis, $distritos)) {

                $distritos[$nomeDis] =
                    AdministrativeDivision::create([
                        'name' => $nomeDis,
                        'level' => '1',
                    ]);;
            }

            // Create Concelho if it doesn't exist yet
            $nomeCon = trim($freg['concelho']);
            $conKey = $nomeDis . $nomeCon;
            if (!array_key_exists($conKey, $concelhos)) {
                $concelhos[$conKey] =
                    AdministrativeDivision::create([
                        'name' => $nomeCon,
                        'level' => '2',
                        'parent_id' => $distritos[$nomeDis]->id
                    ]);
            }

            $fregName = trim(
                Str::replace(
                    ['União das freguesias de ', $nomeCon . ' - '],
                    '',
                    $freg['freguesia']
                )
            );

            // Create Freguesia
            AdministrativeDivision::create([
                'name' => $fregName,
                'level' => '3',
                'parent_id' => $concelhos[$conKey]->id
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\n" . sizeof($distritos) . ' distritos created');
        $this->command->info(sizeof($concelhos) . ' concelhos created');
        $this->command->info(sizeof($freguesias) . ' freguesias created');
    }
}
