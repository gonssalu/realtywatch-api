<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Database\Seeders\RandomHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $all_status = ['available' => 85, 'unavailable' => 13, 'unknown' => 2];
        $all_types = ['house' => 30, 'apartment' => 30, 'office' => 6, 'shop' => 6, 'warehouse' => 3, 'garage' => 10, 'land' => 10, 'other' => 5];
        $all_listing = ['sale' => 45, 'rent' => 40, 'both' => 15];

        $listing_type = RandomHelper::RandomWeightedElement($all_listing);
        $type = RandomHelper::RandomWeightedElement($all_types);
        $status = RandomHelper::RandomWeightedElement($all_status);

        $useful_area = $this->faker->biasedNumberBetween(100, 680);
        $gross_area = $useful_area + $this->faker->biasedNumberBetween(40, 400);
        // wc, typology
        $typology = null;
        $wc = null;
        if ($type != 'land') {
            $typology = $this->faker->biasedNumberBetween(1, 20, function ($x) {
                return pow($x, 3);
            });
            $wc = round($typology / 3) + $this->faker->numberBetween(0, 2);
            if ($wc = 0) $wc = 1;
        }

        return [
            'user_id' => User::factory(),
            'quantity' => ($this->faker->numberBetween(1, 30) == 8 ?
                $this->faker->biasedNumberBetween(
                    2,
                    8,
                    function ($x) {
                        return pow($x, 2);
                    }
                ) : 1),
            'listing_type' => $listing_type,
            'title' => 'Something went wrong while seeding the database',
            'description' => $this->faker->text,
            /*'cover_url' => $this->faker->text,*/
            'useful_area' => $useful_area,
            'gross_area' => $gross_area,
            'type' => $type,
            'typology' => $typology,
            'wc' => $wc,
            'rating' => $this->faker->numberBetween(1, 10),
            /*'current_price_sale' => $this->faker->randomFloat(0, 0, 10000000),*/
            /*'current_price_rent' => $this->faker->randomFloat(0, 0, 10000000),*/
            'status' => $status,
        ];
    }
}
