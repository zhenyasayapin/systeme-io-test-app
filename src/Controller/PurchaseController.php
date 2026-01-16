<?php

namespace App\Controller;

use App\DTO\PurchaseDTO;
use App\Service\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PurchaseService $purchaseService,
    ): JsonResponse {
        if (empty($request->getContent())) {
            throw new \InvalidArgumentException('No data provided');
        }

        $dto = $serializer->deserialize($request->getContent(), PurchaseDTO::class, 'json');

        $violations = $validator->validate($dto);

        if (count($violations) > 0) {
            return $this->json($violations, 400);
        }

        return $this->json($purchaseService->purchase($dto), 201);
    }
}
