<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyTag;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyTag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'tag_id' => Tag::factory(),
        ];
    }
}
