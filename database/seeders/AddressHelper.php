<?php

namespace Database\Seeders;

use App\Models\AdministrativeDivision;
use Arr;
use Str;

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
        $weightedArray = ['locs' => [], 'weights' => []];
        foreach ($locations as $lockey => $loc) {
            $weightedArray['locs'][$lockey] = $loc;
            $weightedArray['weights'][$lockey] = $loc['weight'];
        }
        return $weightedArray;
    }

    public static function GetRandomCoords($weightedArray)
    {
        $locKey = SeederHelper::RandomWeightedElement($weightedArray['weights']);
        $cfgLoc = $weightedArray['locs'][$locKey];
        $coords = self::GenRndCoordsAroundPoint($cfgLoc['coords']['lat'], $cfgLoc['coords']['lon'], $cfgLoc['radius']);
        return $coords;
    }

    private static function GetOSMData($curlHandle, $coords)
    {
        $url = config('factory.address.api.url');
        $url .= '&lat=' . $coords['lat'] . '&lon=' . $coords['lon'];

        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curlHandle,
            CURLOPT_HTTPHEADER,
            array('User-Agent: ' . config('factory.address.api.user_agent'))
        );
        $json = curl_exec($curlHandle);

        $data = json_decode($json, true);
        return $data;
    }

    public static function GetRandomAddress($curlHandle, $weightedArray)
    {
        $coords = self::GetRandomCoords($weightedArray);
        $osm = self::GetOSMData($curlHandle, $coords);

        $address = $osm['address'];

        // Remove country name from full address.
        $countryName = $address['country'];
        $full_address = Str::replace(
            ', ' . $countryName,
            '',
            $osm['display_name']
        );

        // Remove city district & others from full address.
        $removableAttr = [
            3 => ['city_district'],
            2 => ['township', 'town', 'city'],
            1 => ['county']
        ];
        $removed = false;

        $admArr = [];
        $addressTitle = $full_address;
        foreach ($removableAttr as $raKey => $attr) {
            foreach ($attr as $a) {
                if (!array_key_exists($a, $address))
                    continue;

                $thisAdm = $address[$a];

                // If nothing has been removed yet, substring the full address
                if (!$removed) {
                    $matchTxt = ', ' . $thisAdm;
                    if (Str::contains($full_address, $matchTxt)) {
                        $pos = strpos($full_address, $matchTxt);
                        if ($pos !== false)
                            $full_address = substr($full_address, 0, $pos);
                    }
                    $addressTitle = $thisAdm;
                    $removed = true;
                }

                // Get administrative division id
                $adm = AdministrativeDivision::query()
                    ->whereName($thisAdm)
                    ->whereLevel($raKey)->first('id');
                if ($adm != null)
                    $admArr['adm' . $raKey . '_id'] = $adm->id;
                break;
            }
        }

        // Extract postal code
        $postalCode = null;
        if (array_key_exists('postcode', $address))
            $postalCode = $address['postcode'];


        // Join address info together
        $address = array_merge([
            'postal_code' => $postalCode,
            'full_address' => $full_address,
            'coordinates' => [
                $osm['lat'], $osm['lon'],
            ],
            'address_title' => $addressTitle
        ], $admArr);

        return $address;
    }
}
