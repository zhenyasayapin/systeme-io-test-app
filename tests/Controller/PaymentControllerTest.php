<?php

namespace App\Tests\Controller;

use App\Factory\PurchaseFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PaymentControllerTest extends WebTestCase
{
    #[DataProvider('successfullyPayProvider')]
    public function testSuccessfullyPay(array $json, float $price): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->jsonRequest('POST', '/purchase', $json);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($response);

        $this->assertArrayHasKey('data', $response);
        $data = $response['data'];
        $this->assertNotEmpty($data);

        $this->assertArrayHasKey('id', $data);

        $persistedPurchase = PurchaseFactory::repository()->find($data['id']);

        $this->assertNotNull($persistedPurchase);
        $this->assertEquals($json['product'], $persistedPurchase->getProduct()->getId());
        $this->assertEquals($price, $persistedPurchase->getAmount());
    }

    public static function successfullyPayProvider(): iterable
    {
        yield [
            'json' => [
                'product' => 1,
                'taxNumber' => 'FRAB123456789',
                'couponCode' => 'F25',
                'paymentProcessor' => 'paypal',
            ],
            'price' => 95,
        ];

        yield [
            'json' => [
                'product' => 1,
                'taxNumber' => 'IT12345678901',
                'couponCode' => 'P10',
                'paymentProcessor' => 'stripe',
            ],
            'price' => 109.8,
        ];
    }
}
