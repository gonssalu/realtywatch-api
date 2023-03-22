<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Property;
use App\Models\PropertyOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyOfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyOffer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'description' => $this->faker->boolean ? $this->faker->text : null,
            'listing_type' => 'sale',
        ];
    }
}
