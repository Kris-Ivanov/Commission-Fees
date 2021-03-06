<?php

namespace App\Processor;

use App\Operation;

class CashInProcessor
{
    const FEE_PERCENTAGE = 0.03;
    const MAXIMUM_FEE = [
        'amount' => 5.00,
        'currency' => Operation::CURRENCY_EUR,
    ];

    /**
     * Calculate Fee for Cash In Operation
     *
     * @param Operation $operation
     *
     * @return float
     */
    public function calculateFee(Operation $operation): float
    {
        $maximumFee = $this->getMaximumFee($operation);

        $fee = ($operation->getAmount() * self::FEE_PERCENTAGE) / 100;

        if ($fee > $maximumFee) {
            return $maximumFee;
        }

        return $fee;
    }

    /**
     * Get Maximum Fee in Operation's Currency for Cash In
     *
     * @param Operation $operation
     *
     * @return float
     */
    public function getMaximumFee(Operation $operation): float
    {
        $currency = $operation->getCurrency();

        if ($currency === self::MAXIMUM_FEE['currency']) {
            return self::MAXIMUM_FEE['amount'];
        }

        return CommissionProcessor::convertAmount($currency, self::MAXIMUM_FEE['amount']);
    }
}