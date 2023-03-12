<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ListTag;
use App\Models\MyList;
use App\Models\PropertyList;
use App\Models\Tag;

class ListTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ListTag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'list_id' => PropertyList::factory(),
            'tag_id' => Tag::factory(),
        ];
    }
}
