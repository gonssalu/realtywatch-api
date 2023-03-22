<?php

namespace Database\Seeders\helpers;

class SeederHelper
{
    public static function RandomWeightedElement($array)
    {
        $total_weight = array_sum($array);
        $random_weight = rand(1, $total_weight);
        foreach ($array as $key => $value) {
            $random_weight -= $value;
            if ($random_weight <= 0) {
                return $key;
            }
        }
    }

    public static function ReadCsvData($storage_path): array
    {
        $filename = storage_path($storage_path);
        $delimiter = ';';
        if (!file_exists($filename) || !is_readable($filename)) {
            return [];
        }
        $header = null;
        $data = [];
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
}
