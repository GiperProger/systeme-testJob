<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $price = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: PaymentHash::class)]
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function __toString() : string {
        return $this->getName() . ' ('. $this->getPrice() .' евро)';
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
            $paymentHash->setProduct($this);
        }

        return $this;
    }

    public function removePaymentHash(PaymentHash $paymentHash): static
    {
        if ($this->paymentHashes->removeElement($paymentHash)) {
            // set the owning side to null (unless already changed)
            if ($paymentHash->getProduct() === $this) {
                $paymentHash->setProduct(null);
            }
        }

        return $this;
    }
}
