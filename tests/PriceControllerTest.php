<?php

namespace App\Tests;

use App\Factory\ProductBasePriceFactory;
use App\Factory\ProductFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class PriceControllerTest extends WebTestCase
{
    use Factories;

    #[DataProvider('calculatePriceProvider')]
    public function testSuccessfulPriceCalculation(
        array $basePrice,
        int $calculatedPrice,
        ?string $taxNumber = null,
        ?string $couponCode = null,
    ): void {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $product = ProductFactory::createOne([
            'basePrice' => ProductBasePriceFactory::createOne($basePrice),
        ]);

        $client->jsonRequest('POST', '/calculate-price', [
            'product' => $product->getId(),
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('amount', $response);
        $this->assertArrayHasKey('currency', $response);
        $this->assertEquals($calculatedPrice, $response['amount']);
        $this->assertEquals($product->getBasePrice()->getCurrency(), $response['currency']);
    }

    public function testFailedPriceCalculation(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->jsonRequest('GET', '/calculate-price');
        $this->assertResponseStatusCodeSame(400);
    }

    public static function calculatePriceProvider(): iterable
    {
        yield [
            'basePrice' => [
                'currency' => 'USD',
                'amount' => 100,
            ],
            'calculatedPrice' => 100,
        ];

        yield [
            'basePrice' => [
                'currency' => 'USD',
                'amount' => 100,
            ],
            'taxNumber' => 'FRAB123456789',
            'calculatedPrice' => 120,
        ];

        yield [
            'basePrice' => [
                'currency' => 'USD',
                'amount' => 100,
            ],
            'couponCode' => 'P50',
            'calculatedPrice' => 50,
        ];

        yield [
            'basePrice' => [
                'currency' => 'USD',
                'amount' => 100,
            ],
            'couponCode' => 'F25',
            'calculatedPrice' => 75,
        ];

        yield [
            'basePrice' => [
                'currency' => 'USD',
                'amount' => 100,
            ],
            'taxNumber' => 'FRAB123456789',
            'couponCode' => 'F25',
            'calculatedPrice' => 95,
        ];
    }
}
