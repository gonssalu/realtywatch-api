<?php

namespace Database\Factories;

use App\Models\User;
use Database\Seeders\MediaHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->name;
        return [
            'name' => $name,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt('123456'),
            'photo_url' => MediaHelper::GetUserPhoto($name),
            //'photo_url' => '',
        ];
    }
}
