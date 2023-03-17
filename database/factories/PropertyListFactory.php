<?php

namespace Database\Factories;

use App\Models\PropertyList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyList::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
        ];
    }
}
