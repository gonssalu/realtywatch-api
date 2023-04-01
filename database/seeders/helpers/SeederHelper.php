<?php

namespace Database\Seeders\helpers;

class SeederHelper
{
    /**
     * Returns a random element from an array, with weights defined by the array values.
     *
     * @param  array  $array An associative array where the keys are the elements to choose from, and the values are their weights.
     * @return mixed The randomly chosen element.
     */
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

    /**
     * Reads a CSV file and returns its contents as an array of associative arrays.
     *
     * @param  mixed  $storage_path The path to the CSV file, relative to the storage directory.
     * @return array An array of associative arrays, where each element represents a row of the CSV file.
     *               The keys of each associative array correspond to the CSV file headers.
     *               Returns an empty array if the file does not exist or cannot be read.
     */
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
