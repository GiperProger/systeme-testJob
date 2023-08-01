<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    CONST PRODUCT_ITEMS = [
        'Iphone' => 100,
        'Наушники' => 30,
        'Чехол' => 10
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::PRODUCT_ITEMS as $name => $price){
             $product = new Product();
             $product->setName($name);
             $product->setPrice($price);
             $manager->persist($product);
        }

        $manager->flush();
    }
}
