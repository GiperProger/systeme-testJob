<?php

namespace App\Interfaces\ApiObjects;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface CalculateInterface
{
    public function checkDiscount(float $price): float;
    public static function loadValidatorMetadata(ClassMetadata $metadata): void;
}