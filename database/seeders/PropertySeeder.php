<?php

namespace Database\Seeders;

use App\Models\Property;
use Database\Factories\PropertyFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run($user): void
    {
        $faker = Factory::create();
        $wgArr = AddressHelper::GetWeightedCoordsArrayFromConfig();
        $curlHandle = curl_init();
        dd('WIP FAIL SAFE');
        for ($i = 0; $i < 100; $i++) {

            $osm = AddressHelper::GetRandomAddress($curlHandle, $wgArr);

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                    'title' => 'TITULO',
                    'cover_url' => 'aaa'
                ]
            );

            // Sleep for the defined timeout to comply with OSM API usage policy.
            sleep(intval(config('factory.address.api.timeout')));
        }

        curl_close($curlHandle);
    }
}
