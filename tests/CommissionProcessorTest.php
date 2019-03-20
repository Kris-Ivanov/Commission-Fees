<?php

use App\Operation;
use App\Processor\CashInProcessor;
use App\Processor\CashOutProcessor;
use App\Processor\CommissionProcessor;
use PHPUnit\Framework\TestCase;

class CommissionProcessorTest extends TestCase
{
    /**
     * Test Cash In Commission Fee Calculation and number format
     */
    public function testCalculateCashInCommission(): void
    {
        $cashInProcessor = new CashInProcessor();
        $cashOutProcessor = new CashOutProcessor();
        $commissionProcessor = new CommissionProcessor($cashInProcessor, $cashOutProcessor);

        $operation = new Operation(
            date('Y-m-d'),
            11,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_IN,
            150.00,
            Operation::CURRENCY_EUR
        );

        $commission = $commissionProcessor->calculateCommission($operation);

        $expectedCommission = ($operation->getAmount() * CashInProcessor::FEE_PERCENTAGE) / 100;

        $expectedCommission = number_format(round($expectedCommission, 2), 2);

        $this->assertEquals($expectedCommission, $commission);
    }

    /**
     * Test Cash Out Commission Fee Calculation and number format
     */
    public function testCalculateCashOutCommission(): void
    {
        $cashInProcessor = new CashInProcessor();
        $cashOutProcessor = new CashOutProcessor();
        $commissionProcessor = new CommissionProcessor($cashInProcessor, $cashOutProcessor);

        $operation = new Operation(
            date('Y-m-d'),
            12,
            Operation::USER_TYPE_LEGAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            800.00,
            Operation::CURRENCY_EUR
        );

        $commission = $commissionProcessor->calculateCommission($operation);

        $expectedCommission = ($operation->getAmount() * CashOutProcessor::LEGAL_FEE_PERCENTAGE) / 100;

        $expectedCommission = number_format(round($expectedCommission, 2), 2);

        $this->assertEquals($expectedCommission, $commission);
    }
}