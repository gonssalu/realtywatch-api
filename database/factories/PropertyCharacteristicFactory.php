<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Characteristic;
use App\Models\Property;
use App\Models\PropertyCharacteristic;

class PropertyCharacteristicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyCharacteristic::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'characteristic_id' => Characteristic::factory(),
            'value' => $this->faker->word,
        ];
    }
}
