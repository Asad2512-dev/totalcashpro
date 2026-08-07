<?php

declare(strict_types=1);

namespace App\Support\Finance;

final class VatCalculator
{
    /**
     * @return array{net: float, vat: float, gross: float}
     */
    public static function fromGross(float $gross, float $vatRate = 20.0): array
    {
        $gross = round(max(0, $gross), 2);
        if ($vatRate <= 0) {
            return ['net' => $gross, 'vat' => 0.0, 'gross' => $gross];
        }

        $net = round($gross / (1 + ($vatRate / 100)), 2);
        $vat = round($gross - $net, 2);

        return ['net' => $net, 'vat' => $vat, 'gross' => $gross];
    }

    /**
     * @return array{net: float, vat: float, gross: float}
     */
    public static function fromNet(float $net, float $vatRate = 20.0): array
    {
        $net = round(max(0, $net), 2);
        $vat = round($net * ($vatRate / 100), 2);
        $gross = round($net + $vat, 2);

        return ['net' => $net, 'vat' => $vat, 'gross' => $gross];
    }
}
