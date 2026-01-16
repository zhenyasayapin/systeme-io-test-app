<?php

namespace App\Service\PriceModifiers;

use App\DTO\CalculatePriceDTO;
use App\DTO\PriceDTO;
use App\Entity\Coupon;
use App\Enum\CouponFormatEnum;
use App\Repository\CouponRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FixedCouponPriceModifier implements PriceModifierInterface
{
    public function __construct(
        private CouponRepository $couponRepository,
    ) {
    }

    public function supports(CalculatePriceDTO $calculatePriceDto): bool
    {
        return null !== $calculatePriceDto->couponCode && str_starts_with($calculatePriceDto->couponCode, CouponFormatEnum::FIXED->value);
    }

    public function modify(PriceDTO $priceDto, CalculatePriceDTO $calculatePriceDto): void
    {
        /** @var Coupon|null $coupon */
        $coupon = $this->couponRepository->findByCode($calculatePriceDto->couponCode);

        if (null === $coupon) {
            throw new NotFoundHttpException('Coupon not found');
        }

        $priceDto->amount = $priceDto->amount - $coupon->getAmount();
    }
}
