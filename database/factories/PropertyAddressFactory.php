<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyAddress;

class PropertyAddressFactory extends Factory
{
    //WIP
    protected $countries = ["Portugal", "Espanha"];
    protected $country_weights = [9, 1];
    protected $regions = [["Leiria", "Lisboa", "Coimbra", "Santarém"]];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyAddress::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $country = $this->faker->country;

        return [
            'property_id' => Property::factory(),
            'country' => $this->faker->country,
            'region' => $this->faker->state,
            'city' => $this->faker->city,
            'locality' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'street' => $this->faker->streetName,
            'building' => $this->faker->buildingNumber . " " . $this->faker->secondaryAddress,
            'coordinates' => [
                fake()->latitude(),
                fake()->longitude()
            ],
        ];
    }
}
