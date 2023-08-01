<?php

namespace App\Service;

class TaxFormatConverter
{
    public function convertRealTaxToTemplate($taxNumber): string {
        $convertedTaxNumber = preg_replace('/(?<=..)[A-Z]/', 'Y', $taxNumber);
        return preg_replace('/[0-9]/', 'X', $convertedTaxNumber);
    }
}