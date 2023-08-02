<?php

namespace App\Interfaces\ApiObjects;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface PayInterface
{
    public static function loadValidatorMetadata(ClassMetadata $metadata): void;
}