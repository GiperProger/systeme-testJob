<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\PaymentProcessor;

class PaymentProcessorFixtures extends Fixture
{
    CONST PAYMENT_PROCESSORS = [
        'PaypalPaymentProcessor',
        'StripePaymentProcessor'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::PAYMENT_PROCESSORS as $paymentProcessorName){
             $paymentProcessor = new PaymentProcessor();
             $paymentProcessor->setName($paymentProcessorName);
             $manager->persist($paymentProcessor);
        }

        $manager->flush();
    }
}
