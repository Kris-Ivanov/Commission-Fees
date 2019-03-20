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
     * @var CashInProcessor $cashInProcessor
     */
    private $cashInProcessor;

    /**
     * @var CashOutProcessor $cashOutProcessor
     */
    private $cashOutProcessor;

    /**
     * CommissionProcessor constructor.
     * @param CashInProcessor $cashInProcessor
     * @param CashOutProcessor $cashOutProcessor
     */
    public function __construct(CashInProcessor $cashInProcessor, CashOutProcessor $cashOutProcessor)
    {
        $this->cashInProcessor = $cashInProcessor;
        $this->cashOutProcessor = $cashOutProcessor;
    }

    /**
     * Get Commission Fee for given Operation
     *
     * @param Operation $operation
     *
     * @return string
     */
    public function calculateCommission(Operation $operation): string
    {
        if ($operation->getOperationType() === Operation::OPERATION_TYPE_CASH_IN) {
            $commission = $this->cashInProcessor->calculateFee($operation);
        } else {
            $commission = $this->cashOutProcessor->calculateFee($operation);
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