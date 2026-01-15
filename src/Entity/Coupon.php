<?php

namespace App\Entity;

use App\Enum\CouponFormatEnum;
use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_coupon_format_amount', columns: ['format', 'amount'])]
#[UniqueEntity(fields: ['format', 'amount'])]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 1, enumType: CouponFormatEnum::class)]
    private ?CouponFormatEnum $format = null;

    #[ORM\Column]
    private ?int $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormat(): ?CouponFormatEnum
    {
        return $this->format;
    }

    public function setFormat(?CouponFormatEnum $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
