<?php

namespace App\Service\PaymentProcessor;

use App\DTO\PriceDTO;
use App\DTO\PurchaseDTO;
use App\Enum\PaymentProcessorEnum;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor as SystemeIoStripePaymentProcessor;

class StripePaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private SystemeIoStripePaymentProcessor $stripePaymentProcessor,
    ) {
    }

    public function supports(PurchaseDTO $purchaseDto): bool
    {
        return $purchaseDto->paymentProcessor === PaymentProcessorEnum::STRIPE->value;
    }

    public function pay(PriceDTO $priceDto): bool
    {
        return $this->stripePaymentProcessor->processPayment((float) $priceDto->amount);
    }
}
