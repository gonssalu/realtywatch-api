<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Characteristic;
use App\Models\User;
use Database\Seeders\RandomHelper;

class CharacteristicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Characteristic::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $lstType = ['numerical' => 30, 'textual' => 50, 'other' => 5];
        $type = RandomHelper::RandomWeightedElement($lstType);
        $wrd = $this->faker->word;

        return [
            'name' => $this->faker->numberBetween(1, 10) == 8 ? $wrd : ucfirst($wrd),
            'type' => $type,
        ];
    }
}
