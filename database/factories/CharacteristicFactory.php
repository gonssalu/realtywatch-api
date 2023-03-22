<?php

namespace Database\Factories;

use App\Models\Characteristic;
use Database\Seeders\helpers\SeederHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $type = SeederHelper::RandomWeightedElement($lstType);
        $wrd = $this->faker->unique()->words(3, true);

        return [
            'name' => $this->faker->numberBetween(1, 10) == 8 ? $wrd : ucfirst($wrd),
            'type' => $type,
        ];
    }
}
