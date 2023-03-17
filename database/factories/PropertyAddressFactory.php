<?php

namespace Database\Factories;

use App\Models\AdministrativeDivision;
use App\Models\Property;
use App\Models\PropertyAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'adm1_id' => AdministrativeDivision::factory(),
            'adm2_id' => AdministrativeDivision::factory(),
            'adm3_id' => AdministrativeDivision::factory(),
            'full_address' => $this->faker->text,
            'coordinates' => $this->faker->word,
        ];
    }
}
