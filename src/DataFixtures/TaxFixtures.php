<?php

namespace App\DataFixtures;

use App\Factory\CountryFactory;
use App\Factory\TaxFactory;
use App\Factory\TaxNumberFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaxFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $taxes = [
            [
                'countryName' => 'Germany',
                'numberPattern' => '^DE[0-9]{9}$',
                'amount' => 0.19
            ],
            [
                'countryName' => 'France',
                'numberPattern' => '^FR[A-Za-z]{2}[0-9]{9}$',
                'amount' => 0.20
            ],
            [
                'countryName' => 'Italy',
                'numberPattern' => '^IT[0-9]{11}$',
                'amount' => 0.22
            ],
            [
                'countryName' => 'Greece',
                'numberPattern' => '^GR[0-9]{9}$',
                'amount' => 0.24
            ],
        ];

        foreach ($taxes as $tax) {
            $country = CountryFactory::createOne([
                'name' => $tax['countryName']
            ]);

            TaxNumberFactory::createOne([
                'tax' => TaxFactory::createOne([
                    'country' => $country,
                    'amount' => $tax['amount']
                ]),
                'pattern' => $tax['numberPattern']
            ]);
        };
    }
}
