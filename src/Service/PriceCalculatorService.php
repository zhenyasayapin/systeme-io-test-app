<?php

namespace App\Service;

use App\DTO\CalculatePriceDTO;
use App\DTO\PriceDTO;
use App\Repository\ProductRepository;
use App\Service\PriceModifiers\CouponPriceModifier;
use App\Service\PriceModifiers\TaxPriceModifier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceCalculatorService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CouponPriceModifier $couponPriceModifier,
        private TaxPriceModifier $taxPriceModifier,
    )
    {
    }

    public function calculate(CalculatePriceDTO $dto): PriceDTO
    {
        $product = $this->productRepository->find($dto->product);

        if (null === $product) {
            throw new NotFoundHttpException('Product not found');
        }

        $calculatedPrice = new PriceDTO();
        $calculatedPrice->amount = $product->getBasePrice()->getAmount();
        $calculatedPrice->currency = $product->getBasePrice()->getCurrency();

        if ($this->taxPriceModifier->supports($dto)) {
            $this->taxPriceModifier->modify($calculatedPrice, $dto);
        }

        if ($this->couponPriceModifier->supports($dto)) {
            $this->couponPriceModifier->modify($calculatedPrice, $dto);
        }

        return $calculatedPrice;
    }
}