<?php

namespace App\Service;

use App\ApiObjects\CalculateObject;
use App\Entity\Coupon;
use App\Entity\PaymentHash;
use App\Entity\PaymentProcessor;
use App\Entity\Product;
use App\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PaymentService
{
    public function __construct(protected EntityManagerInterface $entityManager, protected ValidatorInterface $validator)
    {
    }

    /**
     * @throws Exception
     */
    public function processCalculate(
        $paymentParams,
        $taxFormatConverter
    ): array
    {
        $productEntity = $this->entityManager->getRepository(Product::class)->find($paymentParams['product'] ?? null);
        $couponEntity = $this->entityManager->getRepository(Coupon::class)->findByCode($paymentParams['couponCode'] ?? 1234);
        $paymentProcessorEntity = $this->entityManager->getRepository(PaymentProcessor::class)->find($paymentParams['paymentProcessor'] ?? null);
        $taxEntity = $this->entityManager->getRepository(Tax::class)->findByTemplate(
            $paymentParams['taxNumber'] ?? null,
            $taxFormatConverter
        );

        $calculateObj = new CalculateObject();
        $calculateObj->init($productEntity, $couponEntity, $taxEntity, $paymentProcessorEntity, $this->validator);

        $paymentData = $calculateObj->getPaymentData();

        $this->entityManager->getRepository(PaymentHash::class)->create(
            $paymentData['hash'],
            $productEntity,
            $paymentData['totalPrice'],
            $paymentProcessorEntity
        );

        return ['hash' => $paymentData['hash'], 'totalPrice' => $paymentData['totalPrice']];
    }

    /**
     * @throws Exception
     */
    public function processPayment($payObj, $userPrice, $paymentHash, PaymentProcessorAggregator $paymentProcessorAggregator): ?string
    {
        /** @var PaymentHash $paymentHashEntity */
        $paymentHashEntity = $this->entityManager->getRepository(PaymentHash::class)->findByHash($paymentHash);

        $payObj->init($paymentHashEntity, $userPrice, $this->validator);

        $paymentProcessorAggregator->pay(
            $paymentHashEntity->getTotalPrice(),
            $paymentHashEntity->getPaymentProcessor()->getName()
        );

        $this->entityManager->getRepository(PaymentHash::class)->remove($paymentHashEntity, true);

        return $paymentHashEntity->getProduct()->getName();
    }
}