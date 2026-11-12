<?php

namespace App\Enums;

enum VehicleType: string
{
    case Motorcycle = 'motorcycle';
    case Car = 'car';
    case Bicycle = 'bicycle';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
