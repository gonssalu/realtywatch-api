<?php

namespace Database\Factories;

use App\Models\PropertyList;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ListFactory extends Factory
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
