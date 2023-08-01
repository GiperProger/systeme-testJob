<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Coupon;
use App\Entity\CouponType;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CouponFixtures extends Fixture implements DependentFixtureInterface
{
    const COUPON_CODES = ['d15r1','d15r2','d15r3','d15r4','d15r5','d15r6','d15r7','d15r8','d15r9'];
    

    public function load(ObjectManager $manager): void
    {
        foreach(self::COUPON_CODES as $couponCode){
            $coupon = new Coupon();
            $coupon->setType($this->getRandomCouponType());
            $coupon->setDiscountValue(rand(1, 5));
            $coupon->setCode($couponCode);
            $manager->persist($coupon);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CouponTypeFixtures::class
        ];
    }

    private function getRandomCouponType() : CouponType {
        $delta = rand(0,1);

        return $delta === 1 ? $this->getReference(CouponTypeFixtures::CONST_COUPONT_TYPE . '_coupon_type') : $this->getReference(CouponTypeFixtures::PERCENT_COUPONT_TYPE . '_coupon_type');
    }
}
