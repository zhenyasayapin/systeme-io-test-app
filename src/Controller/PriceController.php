<?php

namespace App\Controller;

use App\DTO\CalculatePriceDTO;
use App\Entity\Coupon;
use App\Entity\TaxNumber;
use App\Enum\CouponFormatEnum;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\TaxNumberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'app_calculate_price')]
    public function calculate(
        Request             $request,
        SerializerInterface $serializer,
        ProductRepository   $productRepository,
        ValidatorInterface  $validator,
        TaxNumberRepository $taxNumberRepository,
        CouponRepository    $couponRepository
    ): JsonResponse {
        try {
            $dto = $serializer->deserialize($request->getContent(), CalculatePriceDTO::class, 'json');
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 400);
        }

        $violations = $validator->validate($dto);

        if (count($violations) > 0) {
            return $this->json($violations, 400);
        }

        $product = $productRepository->find($dto->product);

        if (null === $product) {
            throw $this->createNotFoundException('Product not found');
        }

        $calculatedPrice = $product->getBasePrice()->getAmount();

        if (null !== $dto->taxNumber) {
            /** @var TaxNumber|null $taxNumber */
            $taxNumber = $taxNumberRepository->findByNumber($dto->taxNumber);

            if (null === $taxNumber) {
                throw $this->createNotFoundException('Tax not found');
            }

            $calculatedPrice = $calculatedPrice + ($calculatedPrice * $taxNumber->getTax()->getAmount());
        }

        if (null !== $dto->couponCode) {
            /** @var Coupon|null $coupon */
            $coupon = $couponRepository->findByCode($dto->couponCode);

            if (null === $coupon) {
                throw $this->createNotFoundException('Coupon not found');
            }

            if ($coupon->getFormat() === CouponFormatEnum::PERCENT) {
                $calculatedPrice = $calculatedPrice - ($calculatedPrice / 100 * $coupon->getAmount());
            } else {
                $calculatedPrice = $calculatedPrice - $coupon->getAmount();
            }
        }

        return $this->json([
            'amount' => $calculatedPrice,
            'currency' => $product->getBasePrice()->getCurrency(),
        ]);
    }
}
