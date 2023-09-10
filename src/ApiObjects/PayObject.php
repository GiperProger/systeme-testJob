<?php

namespace App\ApiObjects;

use App\Entity\PaymentHash;
use App\Interfaces\ApiObjects\PayInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


class PayObject implements PayInterface
{
    /**
     * @var PaymentHash|null
     */
    private ?PaymentHash $paymentHash;
    /**
     * @var Float|null
     */
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

    /**
     * @param ClassMetadata $metadata
     * @return void
     */
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

    /**
     * @throws Exception
     */
    public function init($paymentHashEntity, $userPrice, $validator): static
    {
        $this->setPaymentHash($paymentHashEntity);
        $this->setUserPrice($userPrice);

        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }

            throw new Exception(implode(',', $messages));
        }

        return $this;
    }
}