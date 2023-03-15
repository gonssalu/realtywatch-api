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
        $useful_area = $this->faker->biasedNumberBetween(100, 480);
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
            'title' => 'Something went wrong while seeding the database',
            'description' => $this->faker->text,
            /*'cover_url' => $this->faker->text,*/
            'useful_area' => $useful_area,
            'gross_area' => $useful_area + $this->faker->biasedNumberBetween(80, 140),
            'type' => $this->faker->word,
            'typology' => $this->faker->word,
            'rating' => $this->faker->numberBetween(-8, 8),
            'current_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->randomElement(['available', 'sold', 'rented', 'unavailable', 'unknown']),
        ];
    }
}
