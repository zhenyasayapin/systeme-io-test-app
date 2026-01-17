<?php

namespace App\Tests\Unit;

use App\DTO\PriceDTO;
use App\DTO\PurchaseDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaymentProcessor\PaymentProcessorInterface;
use App\Service\PaymentProcessorResolver;
use App\Service\PriceCalculatorService;
use App\Service\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PurchaseServiceTest extends TestCase
{
    public function testMustCallPaymentProcessor(): void
    {
        $paymentProcessor = $this->createMock(PaymentProcessorInterface::class);
        $paymentProcessor->expects($this->once())->method('supports')->willReturn(true);
        $paymentProcessor->expects($this->once())->method('pay')->willReturn(true);

        $resolver = new PaymentProcessorResolver([$paymentProcessor]);

        $priceDto = new PriceDTO();
        $priceDto->amount = 100;
        $priceDto->currency = 'EUR';

        $priceCalculator = $this->createStub(PriceCalculatorService::class);
        $priceCalculator->method('calculate')->willReturn($priceDto);

        $productRepository = $this->createStub(ProductRepository::class);
        $productRepository->method("find")->willReturn(new Product());

        $entityManager = $this->createStub(EntityManagerInterface::class);

        $service = new PurchaseService(
            $priceCalculator,
            $productRepository,
            $entityManager,
            $resolver,
        );

        $purchaseDto = new PurchaseDTO();
        $purchaseDto->product = 0;

        $service->purchase($purchaseDto);
    }
}
