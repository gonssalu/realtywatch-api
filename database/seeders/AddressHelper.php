<?php

namespace Database\Seeders;

class AddressHelper
{
    private static function GenRndCoordsAroundPoint($latitude, $longitude, $radius_in_meters)
    {
        // Converte o raio de metros para graus de latitude e longitude.
        $radius_in_degrees = $radius_in_meters / 111320.0;

        // Gera dois números aleatórios.
        $u = floatval(rand()) / floatval(getrandmax());
        $v = floatval(rand()) / floatval(getrandmax());

        // Converte os números anteriores em coordenadas dentro do círculo.
        $w = $radius_in_degrees * sqrt($u);
        $t = 2 * pi() * $v;
        $x = $w * cos($t);
        $y = $w * sin($t);

        // Calcula as novas coordenadas.
        $new_latitude = $latitude + $y;
        $new_longitude = $longitude + $x;

        return array('lat' => $new_latitude, 'lon' => $new_longitude);
    }

    public static function GetWeightedCoordsArrayFromConfig()
    {
        $locations = config('factory.address.locations');
        $weightedArray = [];
        foreach ($locations as $loc) {
            $weightedArray[$loc] = $loc['weight'];
        }
        return $weightedArray;
    }

    public static function GetRandomCoords($weightedArray)
    {
        $cfgLoc = RandomHelper::RandomWeightedElement($weightedArray);
        $coords = self::GenRndCoordsAroundPoint($cfgLoc['coords']['lat'], $cfgLoc['coords']['lon'], $cfgLoc['radius']);
        return $coords;
    }

    public static function GetAddressFromCoords($coords)
    {
        $url = config('factory.address.api.url');
        $url .= '&lat=' . $coords['lat'] . '&lon=' . $coords['lon'];
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
}
