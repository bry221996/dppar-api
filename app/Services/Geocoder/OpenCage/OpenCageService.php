<?php

namespace App\Services\Geocoder\OpenCage;

use App\Services\Geocoder\GeocoderInterface;
use Illuminate\Support\Facades\Http;

class OpenCageService implements GeocoderInterface
{
    protected $base_url;
    protected $api_key;


    public function __construct()
    {
        $this->base_url = config('services.open_cage.base_url');
        $this->api_key = config('services.open_cage.key');
    }

    public function reverse(float $lat, float $long)
    {
        $response = Http::get($this->base_url . '/geocode/v1/json', [
            'key' => $this->api_key,
            'q' => "$lat+$long"
        ]);

        if ($response->successful()) {
            $responseJson = $response->json();

            if ($responseJson['status']['code'] === 200 && isset($responseJson['results'])) {
                if (count($responseJson['results'])) {
                    return new OpenCageAddress($responseJson['results'][0]['components']);
                }
            }
        }

        return false;
    }
}
