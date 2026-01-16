<?php

namespace App\Service\PriceModifiers;

use App\DTO\CalculatePriceDTO;
use App\DTO\PriceDTO;
use App\Entity\TaxNumber;
use App\Repository\TaxNumberRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxPriceModifier implements PriceModifierInterface
{
    public function __construct(
        private TaxNumberRepository $taxNumberRepository,
    ) {
    }

    public function supports(CalculatePriceDTO $calculatePriceDto): bool
    {
        return null !== $calculatePriceDto->taxNumber;
    }

    public function modify(PriceDTO $priceDto, CalculatePriceDTO $calculatePriceDto): void
    {
        /** @var TaxNumber|null $taxNumber */
        $taxNumber = $this->taxNumberRepository->findByNumber($calculatePriceDto->taxNumber);

        if (null === $taxNumber) {
            throw new NotFoundHttpException('Tax not found');
        }

        $priceDto->amount = $priceDto->amount + ($priceDto->amount * $taxNumber->getTax()->getAmount());
    }
}
