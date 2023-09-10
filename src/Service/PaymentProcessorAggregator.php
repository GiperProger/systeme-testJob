<?php

namespace App\Service;

use App\Service\PaymentProcessors\PaypalPaymentProcessor;
use App\Service\PaymentProcessors\StripePaymentProcessor;
use Exception;
use InvalidArgumentException;

class PaymentProcessorAggregator
{
    /**
     * @throws Exception
     */
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
                    throw new Exception('Unable to pay. The sum out of the limits for selected payment processor. Try to use another payment processor.');
                }
            case 'StripePaymentProcessor':
                $stripePaymentProcessor = new StripePaymentProcessor();
                $paymentResult =  $stripePaymentProcessor->processPayment($price);
                if(!$paymentResult){
                    throw new Exception('Unable to pay. The sum is too small for selected payment processor. Try to use another payment processor.');
                }
                return true;
            default:
                throw new InvalidArgumentException('Payment processor was not found');
        }
    }
}