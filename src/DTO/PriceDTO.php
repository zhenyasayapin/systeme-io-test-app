<?php

namespace App\DTO;

use App\Enum\CurrencyEnum;

class PriceDTO
{
    public float $amount;
    public string $currency;
}