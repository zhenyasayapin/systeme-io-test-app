<?php

namespace App\Service;

use App\DTO\CalculatePriceDTO;
use App\DTO\PriceDTO;
use App\Repository\ProductRepository;

class PriceCalculatorService
{
    public function __construct(
        private ProductRepository $productRepository,
        private iterable $handlers,
    ) {
    }

    public function calculate(CalculatePriceDTO $calculatedPriceDto): PriceDTO
    {
        $product = $this->productRepository->find($calculatedPriceDto->product);

        if (null === $product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $price = new PriceDTO();
        $price->amount = $product->getBasePrice()->getAmount();
        $price->currency = $product->getBasePrice()->getCurrency();

        foreach ($this->handlers as $handler) {
            if ($handler->supports($calculatedPriceDto)) {
                $handler->modify($price, $calculatedPriceDto);
            }
        }

        return $price;
    }
}
