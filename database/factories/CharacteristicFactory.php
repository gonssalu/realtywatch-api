<?php

namespace Database\Factories;

use App\Models\Characteristic;
use Database\Seeders\SeederHelper;
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
        $listType = ['numerical' => 30, 'textual' => 50, 'other' => 5];
        $type = SeederHelper::RandomWeightedElement($listType);
        $word = $this->faker->unique()->word;

        // TODO
        // $word = $this->faker->word;

        return [
            // 'name' => $this->faker->numberBetween(1, 10) == 8 ? $word : ucfirst($word),
            'type' => $type,
            'name' => $word,
        ];
    }
}
