<?php

namespace Database\Seeders;

use App\Models\PropertyList;
use App\Models\Tag;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ListTagSeeder extends Seeder
{
    /**
     * Run the Lists and Tags seeder.
     *
     * @param  mixed  $user The user for which to generate the lists and tags.
     * @param  array  $qty The number of lists and tags to generate, as an array of integers in the following format:
     *                   [$num_lists, $prop_per_list, $num_tags, $tag_per_prop], where:
     *                   - $num_lists: the number of lists to generate.
     *                   - $prop_per_list: an array with two integers, the minimum and maximum number of properties to assign to each list.
     *                   - $num_tags: the number of tags to generate.
     *                   - $tag_per_prop: an array with two integers, the minimum and maximum number of tags to assign to each property.
     */
    public function run($user, $qty): void
    {
        $num_lists = $qty[0];
        $prop_per_list = $qty[1];
        $num_tags = $qty[2];
        $tag_per_prop = $qty[3];

        $faker = Factory::create();
        $userId = $user->id;

        $this->command->info("Generating $num_lists lists for user $user->name please wait...");
        // Generate lists
        $lists = [];
        for ($i = 0; $i < $num_lists; $i++) {
            $lists[] = PropertyList::create([
                'name' => ucfirst($faker->unique()->words($faker->numberBetween(3, 6), true)),
                'description' => $faker->boolean() ? $faker->text : null,
                'user_id' => $userId,
            ]);
        }

        $this->command->info("Generating $num_tags tags for user $user->name please wait...");
        // Generate tags
        $tags = [];
        for ($i = 0; $i < $num_tags; $i++) {
            $tags[] = Tag::create([
                'name' => $faker->unique()->words($faker->numberBetween(1, 2), true),
                'user_id' => $userId,
            ]);
        }

        $properties = $user->properties()->get();

        $this->command->info('Assigning the generated tags & lists to random properties...');

        // Assign tags to properties
        foreach ($properties as $prop) {
            if ($faker->boolean(85)) {
                $tags_for = $faker->randomElements($tags, $faker->numberBetween($tag_per_prop[0], $tag_per_prop[1]));
                $tagIds = array_map(function ($tag) {
                    return $tag->id;
                }, $tags_for);
                $prop->tags()->attach($tagIds);
            }
        }

        // Assign properties to lists
        foreach ($lists as $list) {
            $props_for = $faker->randomElements($properties, $faker->numberBetween($prop_per_list[0], $prop_per_list[1]));
            $propIds = array_map(function ($prop) {
                return $prop->id;
            }, $props_for);
            $list->properties()->attach($propIds);
        }

        $this->command->info('Creating an empty list and tag...');

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
