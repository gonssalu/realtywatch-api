<?php

namespace Database\Seeders;

use App\Models\Property;
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
        dd('DO NOT RUN, WIP');
        $timeout = intval(config('factory.address.api.timeout'));
        $num_props = 2;

        $this->command->warn("A $timeout second timeout will be applied between each address request to respect OpenStreetMap's API usage policy.");
        $this->command->info("Generating $num_props properties for user $user->name please wait...");
        $this->command->warn("This will take at least " . $timeout * $num_props . " seconds to complete.");
        $bar = $this->command->getOutput()->createProgressBar($num_props);

        $faker = Factory::create();
        $wgArr = AddressHelper::GetWeightedCoordsArrayFromConfig();
        $curlHandle = curl_init();

        for ($i = 0; $i < $num_props; $i++) {

            $address = AddressHelper::GetRandomAddress($curlHandle, $wgArr);

            //MEDIA
            //Characteristics
            //Offer Price & History

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                    'cover_url' => 'aaa'
                ]
            );

            //TODO: translate
            $prop->title = $prop->tipology . ' ' . $prop->type . ' ' . $faker->word() . ' in ' . $address['address_title'];
            $prop->save();

            $bar->advance();

            if ($i != $num_props - 1)
                sleep($timeout);
        }
        $bar->finish();
        curl_close($curlHandle);

        $this->command->info("\n$num_props properties have been generated.");
    }
}
