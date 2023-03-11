<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PropertyMedia;

class PropertyMediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyMedia::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => $this->faker->randomNumber(),
            'type' => $this->faker->randomElement(/** enum_attributes **/),
            'url' => $this->faker->url,
        ];
    }
}
