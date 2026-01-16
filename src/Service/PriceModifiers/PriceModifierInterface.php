<?php

namespace App\Service\PriceModifiers;

use App\DTO\CalculatePriceDTO;
use App\DTO\PriceDTO;

interface PriceModifierInterface
{
    public function supports(CalculatePriceDTO $calculatePriceDto): bool;

    public function modify(PriceDTO $priceDto, CalculatePriceDTO $calculatePriceDto): void;
}
