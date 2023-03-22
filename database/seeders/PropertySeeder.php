<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Characteristic;
use App\Models\Property;
use DB;
use Faker\Factory;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function generateAgencies($userId)
    {
        $agencies_to_create = ['iad Portugal', 'Veigas Imobiliária', 'Rainhavip', 'Century 21', 'Engel & Völkers', 'ERA Imobiliária', 'RE/MAX Portugal'];
        $agencies = [];
        foreach ($agencies_to_create as $agtc) {
            $agencies[] = Agency::create(
                [
                    'name' => $agtc,
                    'user_id' => $userId,
                    'logo_url' => 'dwadada',
                ]
            );
        }

        return $agencies;
    }

    public function generateCharacteristics($userId)
    {
        $num_characteristics = 20;
        $characteristics = [];

        $this->command->info('Generating some characteristics...');
        for ($i = 0; $i < $num_characteristics; $i++) {
            $characteristics[] = Characteristic::factory()->create(
                [
                    'user_id' => $userId,
                ]
            );
        }

        $this->command->info("$num_characteristics characteristics were generated\n");

        return $characteristics;
    }

    /**
     * Seed the application's database.
     */
    public function run($user): void
    {
        $faker = Factory::create();
        // dd('DO NOT RUN, WIP');

        $agencies = $this->generateAgencies($user->id);
        $characteristics = $this->generateCharacteristics($user->id);

        $timeout = intval(config('factory.address.api.timeout'));
        $num_props = 10;

        $this->command->warn("A $timeout second timeout will be applied between each address request to respect OpenStreetMap's API usage policy");
        $this->command->info("Generating $num_props properties for user $user->name please wait...");
        $this->command->warn('This will take at least ' . $timeout * $num_props . ' seconds to complete');
        $bar = $this->command->getOutput()->createProgressBar($num_props);

        $wgArr = AddressHelper::GetWeightedCoordsArrayFromConfig();
        $curlHandle = curl_init();

        for ($i = 0; $i < $num_props; $i++) {
            $address = AddressHelper::GetRandomAddress($curlHandle, $wgArr);

            //MEDIA
            //Offer Price & History

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                    'cover_url' => 'aaa',
                ]
            );

            //TODO: translate
            $prop->title = $prop->typology . ' ' . $prop->type . ' ' . $faker->word() . ' em ' . $address['address_title'];
            $prop->save();

            // Add address to property
            unset($address['address_title']);
            $address['property_id'] = $prop->id;
            $address['coordinates'] = DB::raw('POINT(' . $address['coordinates'][0] . ', ' . $address['coordinates'][1] . ')');
            DB::table('property_addresses')->insert($address);

            // Add characteristics to property
            if ($faker->numberBetween(1, 5) != 3) {
                $crc = $faker->randomElements($characteristics, $faker->numberBetween(1, 5), false);
                foreach ($crc as $cr) {
                    $cr->properties()->attach($prop->id, [
                        'value' => $cr->genRandomValue($faker),
                    ]);
                    $cr->save();
                }
            }

            // Add offers to property

            /*$offerTypes = ['sale', 'rent'];
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $ofrTp = $faker->randomElement($offerTypes);
                $prop->offers()->create(
                    [
                        'url' => $faker->url,
                        'description' => $faker->boolean ? $faker->text : null,
                        'listing_type' => $ofrTp,
                        'price' => $ofrTp == 'sale' ?
                            $faker->numberBetween(100000, 1000000) :
                            $faker->numberBetween(500, 5000),
                    ]
                );
            }*/

            $bar->advance();

            if ($i != $num_props - 1) {
                sleep($timeout);
            }
        }

        $bar->finish();
        curl_close($curlHandle);

        $this->command->info("\n$num_props properties have been generated");
    }
}
