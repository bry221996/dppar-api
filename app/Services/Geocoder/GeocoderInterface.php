<?php

namespace App\Services\Geocoder;

interface GeocoderInterface
{
    public function reverse(float $latitude, float $longiture): GeocoderAddressInterface;
}