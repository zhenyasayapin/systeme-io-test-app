<?php

namespace App\DataFixtures;

use App\Enum\CurrencyEnum;
use App\Factory\ProductBasePriceFactory;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => "iPhone",
                'amount' => 100,
                'currency' => CurrencyEnum::EUR->value,
            ],
            [
                'name' => "Headphones",
                'amount' => 20,
                'currency' => CurrencyEnum::EUR->value,
            ],
            [
                'name' => "Phone Case",
                'amount' => 10,
                'currency' => CurrencyEnum::EUR->value,
            ],
        ];

        foreach ($products as $product) {
            ProductFactory::createOne([
                'name' => $product['name'],
                'basePrice' => ProductBasePriceFactory::createOne([
                        'amount' => $product['amount'],
                        'currency' => $product['currency'],
                    ]
                )
            ]);
        }
    }
}
