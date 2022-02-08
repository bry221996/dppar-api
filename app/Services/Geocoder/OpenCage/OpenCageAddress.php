<?php

namespace App\Services\Geocoder\OpenCage;

use App\Services\Geocoder\GeocoderAddressInterface;

class OpenCageAddress implements GeocoderAddressInterface
{
    protected $adressComponent = [];

    public function setAddressComponent(array $addressComponent)
    {
        $this->adressComponent = $addressComponent;
    }

    public function getTown()
    {
        if (isset($this->adressComponent['town'])) return $this->adressComponent['town'];

        if (isset($this->adressComponent['quarter'])) return $this->adressComponent['quarter'];

        if (isset($this->adressComponent['county'])) return $this->adressComponent['county'];

        return isset($this->adressComponent['suburb']) ? $this->adressComponent['suburb'] : '';
    }

    public function getProvince()
    {
        if (isset($this->adressComponent['state'])) return $this->adressComponent['state'];

        if (isset($this->adressComponent['city'])) return $this->adressComponent['city'];

        return isset($this->adressComponent['region']) ? $this->adressComponent['region'] : '';
    }
}
