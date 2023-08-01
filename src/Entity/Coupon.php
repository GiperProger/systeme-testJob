<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CouponType $type = null;

    #[ORM\Column]
    private ?int $discount_value = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?CouponType
    {
        return $this->type;
    }

    public function setType(?CouponType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDiscountValue(): ?int
    {
        return $this->discount_value;
    }

    public function setDiscountValue(int $discount_value): static
    {
        $this->discount_value = $discount_value;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
