<?php

namespace App\Services\Geocoder;

interface GeocoderAddressInterface
{
    public function getProvince();

    public function getTown();
}