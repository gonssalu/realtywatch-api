<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\List;
use App\Models\ListProperty;
use App\Models\Property;

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
            'list_id' => List::factory(),
            'property_id' => Property::factory(),
        ];
    }
}
