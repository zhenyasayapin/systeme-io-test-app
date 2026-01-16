<?php

namespace App\Service;

use App\DTO\PurchaseDTO;
use App\Service\PaymentProcessor\PaymentProcessorInterface;

class PaymentProcessorResolver
{
    public function __construct(
        private iterable $processors,
    ) {
    }

    public function resolve(PurchaseDTO $purchaseDto): ?PaymentProcessorInterface
    {
        /** @var PaymentProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor->supports($purchaseDto)) {
                return $processor;
            }
        }

        return null;
    }
}
