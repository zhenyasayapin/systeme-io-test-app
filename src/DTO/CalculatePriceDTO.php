<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceDTO
{
    #[Assert\NotBlank(message: "You must provide a product id")]
    #[Assert\Positive]
    public int $product;
}