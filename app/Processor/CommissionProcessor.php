<?php

namespace App\Processor;

use App\Operation;

class CommissionProcessor
{
    const CONVERSION_RATES = [
        Operation::CURRENCY_USD => 1.1497,
        Operation::CURRENCY_JPY => 129.53,
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

        return number_format(round($commission, 2), 2);
    }
}