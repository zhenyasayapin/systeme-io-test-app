<?php

namespace App\Service\PaymentProcessors;

use App\DTO\PriceDTO;
use App\DTO\PurchaseDTO;
use App\Enum\PaymentProcessorEnum;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor as SystemeIoPayPalPaymentProcessor;

class PayPalPaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private SystemeIoPayPalPaymentProcessor $paypalPaymentProcessor
    )
    {
    }

    public function supports(PurchaseDTO $purchaseDto): bool
    {
        return $purchaseDto->paymentProcessor === PaymentProcessorEnum::PAYPAL->value;
    }

    public function pay(PriceDTO $priceDto): bool
    {
        try {
            $this->paypalPaymentProcessor->pay($priceDto->amount);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

}