<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MediaSeeder extends Seeder
{
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

        $this->saveMediaFromApi($PHOTO_API_URL, $API_KEY, 'photos', $photoQueries);
        $this->saveMediaFromApi($VIDEO_API_URL, $API_KEY, 'videos', $videoQueries);
    }

    private function getMediaUrlFromRecord($mediaType, $mediaRecord)
    {
        switch ($mediaType) {
            case 'photos':
                return $mediaRecord['src']['original'];
            case 'videos':
                return $mediaRecord['video_files'][0]['link'];
            default:
                return null;
        }
    }

    private function saveMediaFromApi($theApiUrl, $apiKey, $mediaType, $keywords)
    {
        $headers = ['Authorization' => $apiKey];

        foreach ($keywords as $keyword) {
            $apiUrl = "$theApiUrl?query={$keyword}&per_page=80&page=1";

            // Fetch response from the API
            $response = Http::withHeaders($headers)->get($apiUrl);

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
                    $filename = basename($srcUrl);
                    $filepath = "{$directory}/{$filename}";

                    Http::withOptions(['sink' => $filepath])->get($srcUrl);
                }
            }
        }
    }
}
