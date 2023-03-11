<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\PropertyAddress;

class PropertyAddressFactory extends Factory
{
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
        return [
            'property_id' => Property::factory(),
            'country' => $this->faker->country,
            'region' => $this->faker->word,
            'locality' => $this->faker->word,
            'postal_code' => $this->faker->postcode,
            'street' => $this->faker->streetName,
            'coordinates' => $this->faker->word,
        ];
    }
}
