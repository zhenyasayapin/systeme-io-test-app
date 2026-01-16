<?php

namespace App\Service\PaymentProcessors;

use App\DTO\PriceDTO;
use App\DTO\PurchaseDTO;

interface PaymentProcessorInterface
{
    public function supports(PurchaseDTO $purchaseDto): bool;

    public function pay(PriceDTO $priceDto): bool;
}
