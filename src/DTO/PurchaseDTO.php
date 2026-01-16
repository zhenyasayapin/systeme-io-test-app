<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDTO extends CalculatePriceDTO
{
    #[Assert\NotBlank(message: 'You must provide a payment processor')]
    #[Assert\Length(max: 255)]
    public string $paymentProcessor;
}
