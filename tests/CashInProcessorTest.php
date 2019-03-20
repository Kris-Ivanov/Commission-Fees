<?php

use App\Operation;
use App\Processor\CashInProcessor;
use App\Processor\CommissionProcessor;
use PHPUnit\Framework\TestCase;

class CashInProcessorTest extends TestCase
{
    /**
     * Test should return maximum fee
     */
    public function testMaximumFee(): void
    {
        $cashInProcessor = new CashInProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            1,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_IN,
            90000,
            Operation::CURRENCY_EUR
        );

        $fee = $cashInProcessor->calculateFee($operation);

        $this->assertEquals(CashInProcessor::MAXIMUM_FEE['amount'], $fee);
    }

    /**
     * Test should calculate fee by amount and percentage
     */
    public function testCalculateFee(): void
    {
        $cashInProcessor = new CashInProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            2,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_IN,
            200.00,
            Operation::CURRENCY_EUR
        );

        $fee = $cashInProcessor->calculateFee($operation);
        $expectedFee = ($operation->getAmount() * CashInProcessor::FEE_PERCENTAGE) / 100;

        $this->assertEquals($expectedFee, $fee);
    }

    /**
     * Test should calculate maximum fee in given currency based on the conversion rate
     */
    public function testGetMaximumFeeInOtherCurrency(): void
    {
        $cashInProcessor = new CashInProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            3,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_IN,
            100.00,
            Operation::CURRENCY_USD
        );

        $maximumFee = $cashInProcessor->getMaximumFee($operation);
        $expectedMaximumFee = CashInProcessor::MAXIMUM_FEE['amount'] * CommissionProcessor::CONVERSION_RATES[$operation->getCurrency()];

        $this->assertEquals($expectedMaximumFee, $maximumFee);
    }
}