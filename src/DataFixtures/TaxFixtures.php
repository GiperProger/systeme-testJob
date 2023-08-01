<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tax;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TaxFixtures extends Fixture implements DependentFixtureInterface
{
    const TAX_ITEMS = [
        ['iso' => 'DE', 'format' => 'DEXXXXXXXXX', 'percent' => '19'],
        ['iso' => 'IT', 'format' => 'ITXXXXXXXXXXX', 'percent' => '22'],
        ['iso' => 'GR', 'format' => 'GRXXXXXXXXX', 'percent' => '24'],
        ['iso' => 'FR', 'format' => 'FRYYXXXXXXXXX', 'percent' => '20']
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::TAX_ITEMS as $taxItem){
            $tax = new Tax();
            $tax->setCountry($this->getReference($taxItem['iso']));
            $tax->setFormat($taxItem['format']);
            $tax->setPercent($taxItem['percent']);


            $manager->persist($tax);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CountryFixtures::class
        ];
    }
}
