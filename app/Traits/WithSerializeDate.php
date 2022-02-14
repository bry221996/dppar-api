<?php

namespace App\Traits;

use DateTimeInterface;

trait WithSerializeDate
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->toDateTimeString();
    }
}