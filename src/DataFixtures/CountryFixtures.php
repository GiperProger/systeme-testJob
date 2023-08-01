<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    CONST COUNTRY_NAMES = [
        'DE' => 'Германия',
        'IT' => 'Италия',
        'GR' => 'Греция',
        'FR' => 'Франция'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::COUNTRY_NAMES as $iso => $countryName){
             $country = new Country();
             $country->setName($countryName);
             $manager->persist($country);
             $this->addReference($iso, $country);
        }

        $manager->flush();
    }
}
