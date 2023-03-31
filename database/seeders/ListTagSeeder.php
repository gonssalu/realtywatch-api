<?php

namespace Database\Seeders;

use App\Models\PropertyList;
use App\Models\Tag;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ListTagSeeder extends Seeder
{
    /**
     * Run Lists and Tags seeder.
     */
    public function run($user): void
    {
        $num_lists = 7;
        $prop_per_list = [8, 16];
        $num_tags = 15;
        $tag_per_prop = [1, 5];

        $faker = Factory::create();
        $userId = $user->id;

        // Generate lists
        $lists = [];
        for ($i = 0; $i < $num_lists; $i++) {
            $lists[] = PropertyList::create([
                'name' => ucfirst($faker->unique()->words($faker->numberBetween(3, 6), true)),
                'description' => $faker->boolean() ? $faker->text : null,
                'user_id' => $userId,
            ]);
        }

        // Generate tags
        $tags = [];
        for ($i = 0; $i < $num_tags; $i++) {
            $tags[] = Tag::create([
                'name' => $faker->unique()->words($faker->numberBetween(1, 2), true),
                'user_id' => $userId,
            ]);
        }

        $properties = $user->properties()->get();

        // Assign tags to properties
        foreach ($properties as $prop) {
            if ($faker->boolean(85))
                $prop->tags()->attach($faker->randomElements($tags, $faker->numberBetween($tag_per_prop[0], $tag_per_prop[1])));
        }

        // Assign properties to lists
        foreach ($lists as $list) {
            $list->properties()->attach($faker->randomElements($properties, $faker->numberBetween($prop_per_list[0], $prop_per_list[1])));
        }

        // Create an empty list
        PropertyList::create([
            'name' => 'An empty list',
            'description' => 'This list is empty',
            'user_id' => $userId,
        ]);

        // Create an empty tag
        Tag::create([
            'name' => 'empty tag',
            'user_id' => $userId,
        ]);
    }
}
