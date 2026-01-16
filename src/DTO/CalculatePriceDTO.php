<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceDTO
{
    #[Assert\NotBlank(message: 'Product id is required')]
    #[Assert\Positive]
    public int $product;

    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[A-Z]{2,4}[0-9]{9,11}$/', message: 'Provided tax number has invalid format')]
    public ?string $taxNumber = null;

    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[A-Z][0-9]+$/', message: 'Provided coupon has invalid format')]
    public ?string $couponCode = null;
}
