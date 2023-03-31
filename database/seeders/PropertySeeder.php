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
    private $PROPERTY_STORAGE_DIR_PATH = 'app/media/properties';
    private $PROPERTY_PUBLIC_STORAGE_PATH = 'public/properties';
    private $AGENCY_STORAGE_DIR_PATH = 'app/media/agencies';
    private $AGENCY_PUBLIC_STORAGE_PATH = 'public/agencies';

    private $PREFIX = 'S';

    public function generateAgencies($userId)
    {
        $agencies_to_create = [
            'iad Portugal' => 'iadportugal.jpg', 'Veigas Imobiliária' => 'veigas.jpg', 'Rainhavip' => null, 'Century 21' => 'century21.jpg', 'Engel & Völkers' => 'engelsandvolkers.jpg', 'ERA Imobiliária' => 'era.jpg', 'RE/MAX Portugal' => 'remax.jpg',
        ];

        $agencies = [];
        foreach ($agencies_to_create as $agency_name => $agency_logo) {
            $agency = Agency::create(
                [
                    'name' => $agency_name,
                    'user_id' => $userId,
                ]
            );

            $this->PREFIX = $agency->id;

            if ($agency_logo != null) {
                $agency->logo_url = $this->saveAgencyMediaInPublicStorage("$this->AGENCY_STORAGE_DIR_PATH/$agency_logo");
                $agency->save();
            }

            $agencies[] = $agency;
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
        $photos_dir = storage_path("$this->PROPERTY_STORAGE_DIR_PATH/photos");
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
        $videos_dir = storage_path("$this->PROPERTY_STORAGE_DIR_PATH/videos");
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

    public function saveMediaInPublicStorage($path_to_save, $path)
    {
        $file = file_get_contents($path);
        $filename = $this->PREFIX . '_' . uniqid();
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $simplePath = $filename . $ext;

        $new_path = "$path_to_save/$simplePath";
        Storage::put($new_path, $file);

        return $simplePath;
    }

    public function savePropertyMediaInPublicStorage($path)
    {
        return $this->saveMediaInPublicStorage($this->PROPERTY_PUBLIC_STORAGE_PATH, $path);
    }

    public function saveAgencyMediaInPublicStorage($path)
    {
        return $this->saveMediaInPublicStorage($this->AGENCY_PUBLIC_STORAGE_PATH, $path);
    }

    public function generateOfferPrice($faker, $initial_price, $perc_change, $min_num_offers, $max_num_offers, $allowLess = false)
    {
        $num_offers = $faker->numberBetween($min_num_offers, $max_num_offers) + 1;
        $min_price = $allowLess ? $initial_price * (1 - $perc_change) : $initial_price + 1;
        $max_price = $initial_price * (1 + $perc_change);
        $price = $initial_price;

        $offers = [];
        for ($i = 0; $i < $num_offers; $i++) {
            $offers[] = $price;
            $price = $faker->numberBetween($min_price, $max_price);
        }

        return $offers;
    }

    public function generateOffers($faker, $agencies, $prop, $type, $initial_price, $perc_change)
    {
        $offerPrices = $this->generateOfferPrice($faker, $initial_price, $perc_change, 0, 3);
        foreach ($offerPrices as $offerPrice) {
            $propOffer = $prop->offers()->create(
                [
                    'agency_id' => ($faker->boolean(5) ? null : $faker->randomElement($agencies)->id),
                    'url' => $faker->url,
                    'description' => $faker->boolean ? $faker->text : null,
                    'listing_type' => $type
                ]
            );

            $histPrices = $this->generateOfferPrice($faker, $offerPrice, $perc_change, 0, 6, true);
            $first = true;
            foreach ($histPrices as $histPrice) {
                $propOffer->priceHistory()->create(
                    [
                        'datetime' => $first ? now()->toDateTimeString('second') : $faker->dateTimeBetween('-1 year', 'now'),
                        'price' => $histPrice,
                        'latest' => $first,
                    ]
                );
                $first = false;
            }
        }
    }

    /**
     * Seed properties
     */
    public function run($user, $num_props): void
    {
        $faker = Factory::create();

        $agencies = $this->generateAgencies($user->id);
        $characteristics = $this->generateCharacteristics($user->id);
        $photos = $this->gatherPhotosInArray();

        $videos = $this->gatherVideosInArray();

        $timeout = intval(config('factory.address.api.timeout'));

        $this->command->warn("A $timeout second timeout will be applied between each address request to respect OpenStreetMap's API usage policy");
        $this->command->info("Generating $num_props properties for user $user->name please wait...");
        $this->command->warn('This will take at least ' . $timeout * $num_props . ' seconds to complete');
        $bar = $this->command->getOutput()->createProgressBar($num_props);

        $wgArr = AddressHelper::GetWeightedCoordsArrayFromConfig();
        $curlHandle = curl_init();

        for ($i = 0; $i < $num_props; $i++) {
            $address = AddressHelper::GetRandomAddress($curlHandle, $wgArr);

            $prop = Property::factory()->create(
                [
                    'user_id' => $user->id,
                ]
            );

            $this->PREFIX = $prop->id;

            // Change title
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
                                'url' => $this->savePropertyMediaInPublicStorage(array_pop($photos['blueprint'])),
                                'type' => 'blueprint',
                                'order' => 0,
                            ]
                        );
                    }

                    if (count($videos) > 0)
                        $prop->media()->create(
                            [
                                'url' => $this->savePropertyMediaInPublicStorage(array_pop($videos)),
                                'type' => 'video',
                                'order' => 0,
                            ]
                        );
                }

                if (count($media) > 0) {
                    $prop->media()->createMany(
                        array_map(
                            function ($url) {
                                return [
                                    'url' => $this->savePropertyMediaInPublicStorage($url),
                                    'type' => 'image',
                                ];
                            },
                            $media
                        )
                    );

                    $prop->cover_url = $prop->media()->first()->url;
                }
            }

            // Add offers to property
            $offerTypes = ['sale' => $prop->current_price_sale, 'rent' => $prop->current_price_rent];

            foreach ($offerTypes as $ot => $offer_listing_price) {
                if ($offer_listing_price != null)
                    $this->generateOffers($faker, $agencies, $prop, $ot, $offer_listing_price, 0.0625);
            }

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
