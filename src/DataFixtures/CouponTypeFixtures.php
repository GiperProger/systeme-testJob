<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\CouponType;

class CouponTypeFixtures extends Fixture
{
    CONST CONST_COUPONT_TYPE = 'const';
    CONST PERCENT_COUPONT_TYPE = 'percent';

    CONST COUPON_TYPES = [
        self::CONST_COUPONT_TYPE,
        self::PERCENT_COUPONT_TYPE
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::COUPON_TYPES as $couponTypeName){
             $couponType = new CouponType();
             $couponType->setName($couponTypeName);
             $manager->persist($couponType);

             $this->addReference($couponTypeName . '_coupon_type', $couponType);
        }

        $manager->flush();
    }
}
