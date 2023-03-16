<?php

namespace Database\Seeders;


class RandomHelper
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
}
