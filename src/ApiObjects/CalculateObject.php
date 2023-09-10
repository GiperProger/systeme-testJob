<?php

namespace App\ApiObjects;

use App\Entity\Coupon;
use App\Entity\CouponType;
use App\Entity\PaymentProcessor;
use App\Entity\Product;
use App\Entity\Tax;
use App\Interfaces\ApiObjects\CalculateInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 *
 */
class CalculateObject implements CalculateInterface
{
    /** @var Product|null  */
    private ?Product $product;

    /** @var Tax|null  */
    private ?Tax $tax;

    /** @var Coupon|null  */
    private ?Coupon $coupon;

    /** @var PaymentProcessor|null  */
    private ?PaymentProcessor $paymentProcessor;

    /**
     * Get the value of taxNumber
     *
     * @return Tax
     */
    public function getTax(): Tax
    {
        return $this->tax;
    }

    /**
     * Set the value of tax
     *
     * @param string $tax
     *
     * @return self
     */
    public function setTax(?Tax $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get the value of couponCode
     *
     * @return Coupon
     */
    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }

    /**
     * Set the value of couponCode
     *
     * @param Coupon|null $coupon
     *
     * @return self
     */
    public function setCoupon(?Coupon $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Get total price based on product price, coupon code and tax number
     *
     * @return array
     * @throws Exception
     */
    public function getPaymentData(): array
    {
        $discountPrice = $this->checkDiscount($this->product->getPrice());
        $totalPrice = $discountPrice + $discountPrice / 100 * $this->tax->getPercent();
        $hash = bin2hex(random_bytes(10));

        return ['totalPrice' => $totalPrice, 'hash' => $hash];
    }

    /**
     * Get the value of product
     *
     * @return ?Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * Set the value of product
     *
     * @param ?Product $product
     *
     * @return self
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of paymentProcessor
     *
     * @return ?PaymentProcessor
     */
    public function getPaymentProcessor(): ?PaymentProcessor
    {
        return $this->paymentProcessor;
    }

    /**
     * Set the value of paymentProcessor
     *
     * @param ?PaymentProcessor $paymentProcessor
     *
     * @return self
     */
    public function setPaymentProcessor(?PaymentProcessor $paymentProcessor): self
    {
        $this->paymentProcessor = $paymentProcessor;

        return $this;
    }

    /**
     * @param ClassMetadata $metadata
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('tax', new NotBlank([], "Tax number is incorrect. Please check it and try again"));
        $metadata->addPropertyConstraint('product', new NotBlank([], "The product you are tryuing to buy was not found."));
        $metadata->addPropertyConstraint('paymentProcessor', new NotBlank([], "Payment processor was not found."));
    }

    /**
     * @param float $price
     * @return float
     */
    public function checkDiscount(float $price): float
    {
        if ($this->coupon === null) {
            return $price;
        }

        switch ($this->coupon->getType()->getname()) {
            case CouponType::TYPE_CONST;
                if ($price <= $this->coupon->getDiscountValue()) {
                    throw new InvalidArgumentException("Discount value can not be bigger that the price");
                }
                return $price - $this->coupon->getDiscountValue();
            case CouponType::TYPE_PERCENT;
                return $price - $price / 100 * $this->coupon->getDiscountValue();
        }

        throw new InvalidArgumentException("Invalid coupon type");
    }

    /**
     * @throws Exception
     */
    public function init(
        $productEntity,
        $couponEntity,
        $taxEntity,
        $paymentProcessorEntity,
        ValidatorInterface $validator): static
    {
        $this->setProduct($productEntity);
        $this->setCoupon($couponEntity);
        $this->setTax($taxEntity);
        $this->setPaymentProcessor($paymentProcessorEntity);

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