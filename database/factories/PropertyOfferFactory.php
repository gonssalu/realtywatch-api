<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Property;
use App\Models\PropertyOffer;

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
            'property_id' => Property::factory(),
            'url' => $this->faker->url,
            'description' => $this->faker->text,
            'agency_id' => Agency::factory(),
            'listing_type' => 'sale',
        ];
    }
}
