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

        for ($i = 0; $i < 100; $i++) {

            $coords = AddressHelper::GetRandomCoords($wgArr);

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                    'title' => 'TITULO',
                    'cover_url' => 'aaa'
                ]
            );
        }
    }
}
