<?php

namespace App\Entity;

use App\Repository\PaymentHashRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Float_;

#[ORM\Entity(repositoryClass: PaymentHashRepository::class)]
class PaymentHash
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\ManyToOne(inversedBy: 'paymentHashes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?float $total_price = null;

    #[ORM\ManyToOne(inversedBy: 'paymentHashes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentProcessor $payment_processor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getPaymentProcessor(): ?PaymentProcessor
    {
        return $this->payment_processor;
    }

    public function setPaymentProcessor(?PaymentProcessor $payment_processor): static
    {
        $this->payment_processor = $payment_processor;

        return $this;
    }
}
