<?php

namespace Database\Factories;

use App\Models\ListProperty;
use App\Models\Property;
use App\Models\PropertyList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListPropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ListProperty::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'list_id' => PropertyList::factory(),
            'property_id' => Property::factory(),
        ];
    }
}
