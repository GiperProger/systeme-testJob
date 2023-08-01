<?php

namespace App\ApiObjects;

use App\Entity\PaymentHash;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


class PayObject
{
    private ?PaymentHash $paymentHash;
    private ?Float $userPrice;

    /**
     * Get the value of paymentHash
     *
     * @return ?PaymentHash
     */
    public function getPaymentHash(): ?PaymentHash
    {
        return $this->paymentHash;
    }

    /**
     * Set the value of paymentHash
     *
     * @param ?PaymentHash $paymentHash
     *
     * @return self
     */
    public function setPaymentHash(?PaymentHash $paymentHash): self
    {
        $this->paymentHash = $paymentHash;

        return $this;
    }

    /**
     * Get the value of userPrice
     *
     * @return ?Float
     */
    public function getUserPrice(): ?Float
    {
        return $this->userPrice;
    }

    /**
     * Set the value of userPrice
     *
     * @param ?Float $userPrice
     *
     * @return self
     */
    public function setUserPrice(?Float $userPrice): self
    {
        $this->userPrice = $userPrice;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $callback = function (PayObject $payObject, ExecutionContextInterface $context, mixed $payload): void {
            if($payObject->paymentHash === null){
                $context->addViolation('Unable to buy. Please recalculate and try again.', array(), null);
                return;
            }
            if($payObject->paymentHash->getTotalPrice() !== $payObject->userPrice){
                $context->addViolation('The sum you are trying to pay does not fit the original price', array(), null);
            }
        };

        $metadata->addConstraint(new Assert\Callback ($callback));

    }
}