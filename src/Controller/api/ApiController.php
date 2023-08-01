<?php

namespace App\Controller\api;

use App\ApiObjects\CalculateObject;
use App\ApiObjects\PayObject;
use App\Entity\Coupon;
use App\Entity\PaymentHash;
use App\Entity\PaymentProcessor;
use App\Entity\Product;
use App\Entity\Tax;
use App\Service\PaymentProcessorAggregator;
use App\Service\TaxFormatConverter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    public function __construct(protected ValidatorInterface $validator)
    {
    }

    #[Route(path: '/api/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculate(
        Request                $request,
        EntityManagerInterface $entityManager,
        TaxFormatConverter     $taxFormatConverter
    ): Response
    {

        $postParams = json_decode($request->getContent(), true);

        $productEntity = $entityManager->getRepository(Product::class)->find($postParams['product'] ?? null);
        $couponEntity = $entityManager->getRepository(Coupon::class)->findByCode($postParams['couponCode'] ?? null);
        $paymentProcessorEntity = $entityManager->getRepository(PaymentProcessor::class)->find($postParams['paymentProcessor'] ?? null);

        if ($postParams['couponCode'] && !$couponEntity) {
            return new JsonResponse([
                'success' => false,
                'data' => [],
                'error' => ['The coupon code you are trying to use is incorrect.']
            ], 400);
        }

        $taxTemplate = $taxFormatConverter->convertRealTaxToTemplate($postParams['taxNumber'] ?? null);
        $taxEntity = $entityManager->getRepository(Tax::class)->findByTemplate($taxTemplate);

        $calculateObj = new CalculateObject();
        $calculateObj->setProduct($productEntity);
        $calculateObj->setCoupon($couponEntity);
        $calculateObj->setTax($taxEntity);
        $calculateObj->setPaymentProcessor($paymentProcessorEntity);

        $errors = $this->validator->validate($calculateObj);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
            return new JsonResponse([
                'success' => false,
                'data' => [],
                'error' => $messages
            ], 400);
        }

        try {
            $paymentData = $calculateObj->getPaymentData();
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'data' => [],
                'error' => [$e->getMessage()]
            ], 400);
        }

        $paymentHashEntity = new PaymentHash();
        $paymentHashEntity->setHash($paymentData['hash']);
        $paymentHashEntity->setProduct($productEntity);
        $paymentHashEntity->setTotalPrice($paymentData['totalPrice']);
        $paymentHashEntity->setPaymentProcessor($paymentProcessorEntity);

        $entityManager->getRepository(PaymentHash::class)->save($paymentHashEntity, true);

        return new JsonResponse([
            'success' => true,
            'data' => ['hash' => $paymentData['hash'], 'totalPrice' => $paymentData['totalPrice']],
            'error' => []],
            200);
    }

    #[Route(path: '/api/pay', name: 'pay', methods: ['POST'])]
    public function pay(
        Request                    $request,
        EntityManagerInterface     $entityManager,
        PaymentProcessorAggregator $paymentProcessorAggregator): Response
    {
        $postParams = json_decode($request->getContent(), true);

        $paymentHash = $postParams['hash'];
        $userPrice = $postParams['userPrice'];

        $paymentHashEntity = $entityManager->getRepository(PaymentHash::class)->findByHash($paymentHash);

        $payObj = new PayObject();
        $payObj->setPaymentHash($paymentHashEntity);
        $payObj->setUserPrice($userPrice);

        $errors = $this->validator->validate($payObj);

        if (count($errors) > 0) {
            $entityManager->getRepository(PaymentHash::class)->remove($paymentHashEntity, true);
            $messages = [];
            foreach ($errors as $violation) {
                $messages[] = $violation->getMessage();
            }
            return new JsonResponse([
                'success' => false,
                'data' => [],
                'error' => $messages
            ], 400);
        }

        $paymentResult = $paymentProcessorAggregator->pay(
            $paymentHashEntity->getTotalPrice(),
            $paymentHashEntity->getPaymentProcessor()->getName()
        );

        if ($paymentResult === false) {
            $entityManager->getRepository(PaymentHash::class)->remove($paymentHashEntity, true);
            return new JsonResponse([
                'success' => false,
                'data' => [],
                'error' => ['Unable to pay. The sum out of the limits for selected payment processor. Try to use another payment processor.']
            ], 400);
        }

        $entityManager->getRepository(PaymentHash::class)->remove($paymentHashEntity, true);

        return new JsonResponse([
            'success' => true,
            'data' => ['message' => 'Payment is successfull. Now you are owner of ' . $paymentHashEntity->getProduct()->getName()],
            'error'
        ], 200);
    }
}