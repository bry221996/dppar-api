<?php

namespace App\Services\Geocoder\OpenCage;

use App\Services\Geocoder\GeocoderAddressInterface;

class OpenCageAddress implements GeocoderAddressInterface
{
    protected $adressComponent;

    public function __construct($adressComponent)
    {
        $this->adressComponent = $adressComponent;
    }

    public function getTown()
    {
        if (isset($this->adressComponent['town'])) return $this->adressComponent['town'];

        if (isset($this->adressComponent['quarter'])) return $this->adressComponent['quarter'];

        return isset($this->adressComponent['county']) ? $this->adressComponent['county'] : '';
    }

    public function getProvince()
    {
        if (isset($this->adressComponent['state'])) return $this->adressComponent['state'];

        return isset($this->adressComponent['city']) ? $this->adressComponent['city'] : '';
    }
}
