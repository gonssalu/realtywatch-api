<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\User;

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
        return [
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(-1000, 1000),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
            'cover_url' => $this->faker->text,
            'total_area' => $this->faker->randomFloat(0, 0, 9999999999.),
            'gross_area' => $this->faker->randomFloat(0, 0, 9999999999.),
            'type' => $this->faker->word,
            'typology' => $this->faker->word,
            'rating' => $this->faker->numberBetween(-8, 8),
            'current_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->randomElement(['available', 'sold', 'rented', 'unavailable', 'unknown']),
        ];
    }
}
