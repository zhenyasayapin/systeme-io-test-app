<?php

namespace App\DataFixtures;

use App\Enum\CouponFormatEnum;
use App\Factory\CouponFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CouponFactory::new()->sequence([
            ['amount' => 10, 'format' => CouponFormatEnum::PERCENT],
            ['amount' => 25, 'format' => CouponFormatEnum::PERCENT],
            ['amount' => 30, 'format' => CouponFormatEnum::PERCENT],
            ['amount' => 5, 'format' => CouponFormatEnum::FIXED],
            ['amount' => 10, 'format' => CouponFormatEnum::FIXED],
            ['amount' => 15, 'format' => CouponFormatEnum::FIXED],
        ])->create();
    }
}
