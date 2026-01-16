<?php

namespace App\Controller;

use App\DTO\CalculatePriceDTO;
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
    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST'])]
    public function calculate(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PriceCalculatorService $priceCalculatorService,
    ): JsonResponse {
        if (empty($request->getContent())) {
            throw new \InvalidArgumentException('No data provided');
        }

        $calculatePriceDto = $serializer->deserialize($request->getContent(), CalculatePriceDTO::class, 'json');

        $violations = $validator->validate($calculatePriceDto);

        if (count($violations) > 0) {
            return $this->json($violations, 400);
        }

        return $this->json($priceCalculatorService->calculate($calculatePriceDto));
    }
}
