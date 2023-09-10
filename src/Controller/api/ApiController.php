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
        TaxFormatConverter     $taxFormatConverter,
        CalculateObject        $calculateObj
    ): Response
    {

        $postParams = json_decode($request->getContent(), true);
        $productEntity = $entityManager->getRepository(Product::class)->find($postParams['product'] ?? null);

        try {
            $couponEntity = $entityManager->getRepository(Coupon::class)->findByCode($postParams['couponCode'] ?? 1234);
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        $paymentProcessorEntity = $entityManager->getRepository(PaymentProcessor::class)->find($postParams['paymentProcessor'] ?? null);

        $taxEntity = $entityManager->getRepository(Tax::class)->findByTemplate(
            $postParams['taxNumber'] ?? null,
                      $taxFormatConverter
        );

        try {
            $calculateObj->init($productEntity, $couponEntity, $taxEntity, $paymentProcessorEntity, $this->validator);
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        try {
            $paymentData = $calculateObj->getPaymentData();
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        $entityManager->getRepository(PaymentHash::class)->create(
            $paymentData['hash'],
            $productEntity,
            $paymentData['totalPrice'],
            $paymentProcessorEntity
        );

        $responseData = ['hash' => $paymentData['hash'], 'totalPrice' => $paymentData['totalPrice']];

        return new JsonResponse(['success' => true, 'data' => $responseData, 'error' => []], 200);
    }

    #[Route(path: '/api/pay', name: 'pay', methods: ['POST'])]
    public function pay(
        Request                    $request,
        EntityManagerInterface     $entityManager,
        PaymentProcessorAggregator $paymentProcessorAggregator,
        PayObject                  $payObj ): Response
    {
        $postParams = json_decode($request->getContent(), true);
        $paymentHash = $postParams['hash'];
        $userPrice = $postParams['userPrice'];

        $paymentHashEntity = $entityManager->getRepository(PaymentHash::class)->findByHash($paymentHash);

        try {
            $payObj->init($paymentHashEntity, $userPrice, $this->validator);
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => $e->getMessage()], 400);
        }

        try {
            $paymentProcessorAggregator->pay(
                $paymentHashEntity->getTotalPrice(),
                $paymentHashEntity->getPaymentProcessor()->getName()
            );
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        $entityManager->getRepository(PaymentHash::class)->remove($paymentHashEntity, true);

        return new JsonResponse([
            'success' => true,
            'data' => ['message' => 'Payment is successfull. Now you are owner of ' . $paymentHashEntity->getProduct()->getName()],
            'error'
        ], 200);
    }
}