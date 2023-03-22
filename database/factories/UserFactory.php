<?php

namespace Database\Factories;

use App\Models\User;
use Database\Seeders\helpers\MediaHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $name = $firstName . ' ' . $lastName;
        $email = Str::lower($firstName . '.' . $lastName . '@example.com');

        return [
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('123456'),
            'photo_url' => MediaHelper::GetUserPhoto($name),
        ];
    }
}
