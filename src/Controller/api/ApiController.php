<?php

namespace App\Controller\api;

use App\ApiObjects\PayObject;
use App\Service\PaymentService;
use App\Service\PaymentProcessorAggregator;
use App\Service\TaxFormatConverter;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(
        protected PaymentService     $paymentService,
    )
    {
    }

    #[Route(path: '/api/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculate(
        Request                $request,
        TaxFormatConverter     $taxFormatConverter
    ): Response
    {
        $postParams = json_decode($request->getContent(), true);

        try {
            $responseData = $this->paymentService->processCalculate(
                $postParams,
                $taxFormatConverter
            );
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        return new JsonResponse(['success' => true, 'data' => $responseData, 'error' => []], 200);
    }

    #[Route(path: '/api/pay', name: 'pay', methods: ['POST'])]
    public function pay(
        Request                    $request,
        PaymentProcessorAggregator $paymentProcessorAggregator,
        PayObject                  $payObj): Response
    {
        $postParams = json_decode($request->getContent(), true);
        $paymentHash = $postParams['hash'];
        $userPrice = $postParams['userPrice'];

        try {
            $productName = $this->paymentService->processPayment($payObj, $userPrice, $paymentHash, $paymentProcessorAggregator);
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'data' => [], 'error' => [$e->getMessage()]], 400);
        }

        return new JsonResponse([
            'success' => true,
            'data' => ['message' => 'Payment is successfull. Now you are owner of ' . $productName],
            'error'
        ], 200);
    }
}