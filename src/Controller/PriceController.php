<?php

namespace App\Controller;

use App\DTO\CalculatePriceDTO;
use App\Entity\Coupon;
use App\Entity\TaxNumber;
use App\Enum\CouponFormatEnum;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\TaxNumberRepository;
use App\Service\PriceCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'app_calculate_price')]
    public function calculate(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorInterface  $validator,
        PriceCalculatorService $priceCalculatorService,
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

        try {
            return $this->json($priceCalculatorService->calculate($dto));
        } catch (NotFoundHttpException $exception) {
            return $this->json($exception->getMessage(), 400);
        }

    }
}
