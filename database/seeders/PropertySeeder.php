<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Characteristic;
use App\Models\Property;
use Database\Seeders\helpers\AddressHelper;
use DB;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Storage;

class PropertySeeder extends Seeder
{
    private $STORAGE_DIR_PATH = 'app/media/properties';
    private $PUBLIC_STORAGE_PATH = 'public/properties';


    public function generateAgencies($userId)
    {
        $agencies_to_create = [
            'iad Portugal', 'Veigas Imobiliária', 'Rainhavip', 'Century 21', 'Engel & Völkers', 'ERA Imobiliária', 'RE/MAX Portugal',
        ];
        $agencies = [];
        foreach ($agencies_to_create as $agtc) {
            $agencies[] = Agency::create(
                [
                    'name' => $agtc,
                    'user_id' => $userId,
                    'logo_url' => 'dwadada',
                ]
            );
        }

        return $agencies;
    }

    public function generateCharacteristics($userId)
    {
        $num_characteristics = 20;
        $characteristics = [];

        $this->command->info('Generating some characteristics...');
        for ($i = 0; $i < $num_characteristics; $i++) {
            $characteristics[] = Characteristic::factory()->create(
                [
                    'user_id' => $userId,
                ]
            );
        }

        $this->command->info("$num_characteristics characteristics were generated\n");

        return $characteristics;
    }

    public function gatherPhotosInArray()
    {
        $photos_dir = storage_path("$this->STORAGE_DIR_PATH/photos");
        $photos = [];

        //Scan photos_dir for subdirectories and then scan each subdirectory for files
        $photos_subdirs = scandir($photos_dir);
        foreach ($photos_subdirs as $photo_subdir) {
            if ($photo_subdir != '.' && $photo_subdir != '..') {
                $photos[$photo_subdir] = [];

                //Scan each subdir for photos
                foreach (scandir("$photos_dir/$photo_subdir") as $photo) {
                    if ($photo != '.' && $photo != '..')
                        $photos[$photo_subdir][] = "$photos_dir/$photo_subdir/" . $photo;
                }

                shuffle($photos[$photo_subdir]);
            }
        }

        return $photos;
    }

    public function gatherVideosInArray()
    {
        $videos_dir = storage_path("$this->STORAGE_DIR_PATH/videos");
        $videos = [];

        //Scan videos_dir for files
        $video_files = scandir($videos_dir);
        foreach ($video_files as $video_file) {
            if ($video_file != '.' && $video_file != '..') {
                $videos[] = $videos_dir . '/' . $video_file;
            }
        }
        shuffle($videos);

        return $videos;
    }

    public function saveMediaInPublicStorage($path)
    {
        $file = file_get_contents($path);
        $filename = 'S_' . uniqid();
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $simplePath = $filename . $ext;

        $new_path = "$this->PUBLIC_STORAGE_PATH/$simplePath";
        Storage::put($new_path, $file);

        return $simplePath;
    }

    /**
     * Seed the application's database.
     */
    public function run($user): void
    {
        $faker = Factory::create();

        //$agencies = $this->generateAgencies($user->id);
        $characteristics = $this->generateCharacteristics($user->id);
        $photos = $this->gatherPhotosInArray();

        $videos = $this->gatherVideosInArray();

        $timeout = intval(config('factory.address.api.timeout'));
        $num_props = 10;

        $this->command->warn("A $timeout second timeout will be applied between each address request to respect OpenStreetMap's API usage policy");
        $this->command->info("Generating $num_props properties for user $user->name please wait...");
        $this->command->warn('This will take at least ' . $timeout * $num_props . ' seconds to complete');
        $bar = $this->command->getOutput()->createProgressBar($num_props);

        $wgArr = AddressHelper::GetWeightedCoordsArrayFromConfig();
        $curlHandle = curl_init();

        for ($i = 0; $i < $num_props; $i++) {
            $address = AddressHelper::GetRandomAddress($curlHandle, $wgArr);

            //MEDIA
            //Offer Price & History

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                ]
            );

            //TODO: translate
            $prop->title = $prop->typology . ' ' . $prop->type . ' ' . $faker->word() . ' em ' . $address['address_title'];
            $prop->save();

            // Add address to property
            unset($address['address_title']);
            $address['property_id'] = $prop->id;
            $address['coordinates'] = DB::raw('POINT(' . $address['coordinates'][0] . ', ' . $address['coordinates'][1] . ')');
            DB::table('property_addresses')->insert($address);

            // Add characteristics to property
            if ($faker->numberBetween(1, 5) != 3) {
                $crc = $faker->randomElements($characteristics, $faker->numberBetween(1, 5), false);
                foreach ($crc as $cr) {
                    $cr->properties()->attach($prop->id, [
                        'value' => $cr->genRandomValue($faker),
                    ]);
                    $cr->save();
                }
            }
            // Add media to property
            if ($prop->type != 'other') {
                $media = [];

                // Add main photo
                if (count($photos[$prop->type]) > 0)
                    $media[] = array_pop($photos[$prop->type]);

                // If property is a house / apartment
                if ($prop->type == 'house' || $prop->type == 'apartment') {
                    $photoCategories = ['bedroom', 'bathroom', 'kitchen',  'living room',  'interior'];

                    foreach ($photoCategories as $photoCategory)
                        if (count($photos[$photoCategory]) > 0)
                            $media[] = array_pop($photos[$photoCategory]);

                    if (count($photos['other rooms']) > 0 && $faker->boolean(15))
                        $media[] = array_pop($photos['other rooms']);

                    if (count($photos['blueprint']) > 0) {
                        $prop->media()->create(
                            [
                                'url' => $this->saveMediaInPublicStorage(array_pop($photos['blueprint'])),
                                'type' => 'blueprint',
                                'order' => 0,
                            ]
                        );
                    }

                    if (count($videos) > 0)
                        $prop->media()->create(
                            [
                                'url' => $this->saveMediaInPublicStorage(array_pop($videos)),
                                'type' => 'video',
                                'order' => 0,
                            ]
                        );
                }

                if (count($media) > 0)
                    $prop->media()->createMany(
                        array_map(
                            function ($url) {
                                return [
                                    'url' => $this->saveMediaInPublicStorage($url),
                                    'type' => 'image',
                                ];
                            },
                            $media
                        )
                    );
            }

            // Add offers to property

            /*$offerTypes = ['sale', 'rent'];
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $ofrTp = $faker->randomElement($offerTypes);
                $prop->offers()->create(
                    [
                        'url' => $faker->url,
                        'description' => $faker->boolean ? $faker->text : null,
                        'listing_type' => $ofrTp,
                        'price' => $ofrTp == 'sale' ?
                            $faker->numberBetween(100000, 1000000) :
                            $faker->numberBetween(500, 5000),
                    ]
                );
            }*/

            $bar->advance();

            if ($i != $num_props - 1) {
                sleep($timeout);
            }
        }

        $bar->finish();
        curl_close($curlHandle);

        $this->command->info("\n$num_props properties have been generated");
    }
}
