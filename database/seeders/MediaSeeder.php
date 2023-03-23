<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MediaSeeder extends Seeder
{

    // Progress bar
    private $bar;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $PHOTO_API_URL = config('factory.media.property.api.images');
        $VIDEO_API_URL = config('factory.media.property.api.videos');
        $API_KEY = config('factory.media.property.api.key');

        $photoQueries =
            [
                'house',
                'kitchen interior design',
                'living room',
                'bathroom',
                'bedroom',
                'interior design',
                'office',
                'luxury apartment',
                'luxury house',
                'blueprint',
                'garage building',
                'warehouse building',
                'land',
                'retail shop interior design'
            ];

        $videoQueries =
            [
                'real estate',
            ];

        $this->command->info('Fetching photos from API...');
        $this->fetchKeywordsFromApi($PHOTO_API_URL, $API_KEY, 'photos', $photoQueries);
        $this->command->info('-----------------------------------------------');
        $this->command->info('Fetching videos from API...');
        //$this->fetchKeywordsFromApi($VIDEO_API_URL, $API_KEY, 'videos', $videoQueries);
    }

    // Fetch multiple keywords from the API
    private function fetchKeywordsFromApi($theApiUrl, $apiKey, $mediaType, $keywords)
    {
        $this->bar = $this->command->getOutput()->createProgressBar(count($keywords) * 80);
        foreach ($keywords as $keyword) {
            $this->saveMediaFromApi($theApiUrl, $apiKey, $mediaType, $keyword);
            $this->command->warn(" - $keyword");
        }
        $this->bar->finish();
    }

    // Fetch media of a certain keyword from the API and save it to the disk
    private function saveMediaFromApi($theApiUrl, $apiKey, $mediaType, $keyword)
    {
        $apiUrl = "$theApiUrl?query={$keyword}&per_page=80&page=1";

        // Fetch response from the API
        $response = Http::withHeaders(['Authorization' => $apiKey])->get($apiUrl);

        if ($response->ok()) {
            $media = $response->json()[$mediaType];

            // Create a directory for the current keyword's media
            $directory = public_path("$mediaType/{$keyword}");
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Loop through each media and download it
            foreach ($media as $mediaRecord) {
                $srcUrl = $this->getMediaUrlFromRecord($mediaType, $mediaRecord);
                $filename = basename(parse_url($srcUrl, PHP_URL_PATH));
                $filepath = "{$directory}/{$filename}";

                Http::withOptions(['sink' => $filepath])->get($srcUrl);

                $this->bar->advance();
            }
        }
    }

    // Process media record and return the URL of the media
    private function getMediaUrlFromRecord($mediaType, $mediaRecord)
    {
        switch ($mediaType) {
            case 'photos':
                return $mediaRecord['src']['large2x'];
            case 'videos':
                return $mediaRecord['video_files'][0]['link'];
            default:
                return null;
        }
    }
}
