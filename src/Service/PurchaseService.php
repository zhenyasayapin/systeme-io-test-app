<?php

namespace App\Service;

use App\DTO\PurchaseDTO;
use App\Entity\Purchase;
use App\Repository\ProductRepository;
use App\Service\PaymentProcessors\PaymentProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PurchaseService
{
    public function __construct(
        private PriceCalculatorService $priceCalculatorService,
        private ProductRepository      $productRepository,
        private iterable               $processors,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function purchase(PurchaseDTO $purchaseDto): Purchase
    {
        $price = $this->priceCalculatorService->calculate($purchaseDto);

        $product = $this->productRepository->find($purchaseDto->product);

        if ($product === null) {
            throw new \InvalidArgumentException("Product not found");
        }

        $paymentProcessor = null;
        /** @var PaymentProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor->supports($purchaseDto)) {
                $paymentProcessor = $processor;

                break;
            }
        }

        if (null === $paymentProcessor) {
            throw new \InvalidArgumentException("No payment processor found");
        }

        if ($paymentProcessor->pay($price)) {
            $purchase = new Purchase();

            $purchase->setProduct($product);
            $purchase->setCurrency($price->currency);
            $purchase->setAmount($price->amount);

            $this->entityManager->persist($purchase);
            $this->entityManager->flush();

            return $purchase;
        } else {
            throw new BadRequestHttpException("Payment processor failed");
        }
    }
}