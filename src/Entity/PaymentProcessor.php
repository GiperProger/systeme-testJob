<?php

namespace App\Entity;

use App\Repository\PaymentProcessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentProcessorRepository::class)]
class PaymentProcessor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'payment_processor', targetEntity: PaymentHash::class)]
    private Collection $paymentHashes;

    public function __construct()
    {
        $this->paymentHashes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, PaymentHash>
     */
    public function getPaymentHashes(): Collection
    {
        return $this->paymentHashes;
    }

    public function addPaymentHash(PaymentHash $paymentHash): static
    {
        if (!$this->paymentHashes->contains($paymentHash)) {
            $this->paymentHashes->add($paymentHash);
            $paymentHash->setPaymentProcessor($this);
        }

        return $this;
    }

    public function removePaymentHash(PaymentHash $paymentHash): static
    {
        if ($this->paymentHashes->removeElement($paymentHash)) {
            // set the owning side to null (unless already changed)
            if ($paymentHash->getPaymentProcessor() === $this) {
                $paymentHash->setPaymentProcessor(null);
            }
        }

        return $this;
    }
}
