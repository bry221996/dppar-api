<?php

namespace App\Services\Geocoder;

interface GeocoderAddressInterface
{
    public function setAddressComponent(array $addressComponent);

    public function getProvince();

    public function getTown();
}