<?php

namespace App\Processor;

use App\Operation;

class CommissionProcessor
{
    const CONVERSION_RATES = [
        Operation::CURRENCY_USD => 1.1297,
        Operation::CURRENCY_JPY => 119.51,
    ];

    /**
     * Get Commission Fee for given Operation
     *
     * @param Operation $operation
     *
     * @return string
     */
    public static function calculateCommission(Operation $operation): string
    {
        if ($operation->getOperationType() === Operation::OPERATION_TYPE_CASH_IN) {
            $commission = CashInProcessor::calculateFee($operation);
        } else {
            $commission = CashOutProcessor::calculateFee($operation);
        }

        return number_format(round($commission, 2), 2, '.', '');
    }

    /**
     * Get converted amount into given currency
     *
     * @param string $currency
     * @param float $amount
     * @param bool $inEuro
     *
     * @return float
     */
    public static function convertAmount(string $currency, float $amount, bool $inEuro = false): float
    {
        $conversionRate = self::CONVERSION_RATES[$currency];

        if ($inEuro) {
            return $amount / $conversionRate;
        }

        return $amount * $conversionRate;
    }
}