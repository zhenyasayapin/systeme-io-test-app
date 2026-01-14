<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class PriceControllerTest extends WebTestCase
{
    use Factories;

    public function testSuccessfulPriceCalculation(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $product = ProductFactory::createOne();

        $client->jsonRequest('GET', '/calculate-price', [
            'product' => $product->getId()
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('amount', $response);
        $this->assertArrayHasKey('currency', $response);
        $this->assertEquals($product->getBasePrice()->getAmount(), $response['amount']);
        $this->assertEquals($product->getBasePrice()->getCurrency(), $response['currency']);
    }

    public function testFailedPriceCalculation(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->jsonRequest('GET', '/calculate-price');
        $this->assertResponseStatusCodeSame(400);
    }
}
