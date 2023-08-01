<?php

namespace App\Service;

use App\Service\PaymentProcessors\PaypalPaymentProcessor;
use App\Service\PaymentProcessors\StripePaymentProcessor;
use Exception;
use InvalidArgumentException;

class PaymentProcessorAggregator
{
    public static function pay($price, $paymentProcessorClassName): bool
    {
        switch($paymentProcessorClassName)
        {
            case 'PaypalPaymentProcessor':
                $paypalPaymentProcessor = new PaypalPaymentProcessor();
                try{
                     $paypalPaymentProcessor->pay($price);
                     return true;
                }catch(Exception $e){
                    return false;
                }
                break;
            case 'StripePaymentProcessor':
                $stripePaymentProcessor = new StripePaymentProcessor();
                return $stripePaymentProcessor->processPayment($price);
                break;
            default:
                throw new InvalidArgumentException('Payment processor was not found');
        }
    }
}