<?php

namespace App\Enum;

enum PaymentProcessorEnum: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
}
