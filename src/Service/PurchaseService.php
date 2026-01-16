<?php

namespace App\Service;

use App\DTO\PriceDTO;
use App\DTO\PurchaseDTO;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseService
{
    public function __construct(
        private PriceCalculatorService $priceCalculatorService,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private PaymentProcessorResolver $paymentProcessorResolver
    ) {
    }

    public function purchase(PurchaseDTO $purchaseDto): Purchase
    {
        $price = $this->priceCalculatorService->calculate($purchaseDto);
        $product = $this->productRepository->find($purchaseDto->product);

        if (null === $product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $paymentProcessor = $this->paymentProcessorResolver->resolve($purchaseDto);

        if (null === $paymentProcessor) {
            throw new \InvalidArgumentException('Payment processor not found');
        }

        if (!$paymentProcessor->pay($price)) {
            throw new \InvalidArgumentException('Payment processor failed');
        }

        return $this->createPurchase($product, $price);
    }

    private function createPurchase(Product $product, PriceDTO $price): Purchase
    {
        $purchase = new Purchase();

        $purchase->setProduct($product);
        $purchase->setCurrency($price->currency);
        $purchase->setAmount($price->amount);

        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        return $purchase;
    }
}
