<?php

namespace App\DTO;

use App\Enum\CurrencyEnum;

class PriceDTO
{
    public int $amount;
    public string $currency;
}