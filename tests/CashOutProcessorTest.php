<?php

use App\Operation;
use App\Processor\CashOutProcessor;
use App\Processor\CommissionProcessor;
use PHPUnit\Framework\TestCase;

class CashOutProcessorTest extends TestCase
{
    /**
     * Test Natural Person Operation with discount should be free
     */
    public function testNaturalPersonFreeOperation(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            4,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            500.00,
            Operation::CURRENCY_EUR
        );

        $fee = $cashOutProcessor->calculateFee($operation);

        $this->assertEquals(0, $fee);
    }

    /**
     * Test Natural Person Fee with Discount should be charged only for remaining amount
     */
    public function testNaturalPersonFeeAfterDiscount(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            5,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            1500.00,
            Operation::CURRENCY_EUR
        );

        $amount = $operation->getAmount() - CashOutProcessor::NATURAL_WEEKLY_FREE_AMOUNT['amount'];

        $fee = $cashOutProcessor->calculateFee($operation);
        $expectedFee = ($amount * CashOutProcessor::NATURAL_FEE_PERCENTAGE) / 100;

        $this->assertEquals($expectedFee, $fee);
    }

    /**
     * Test Natural Person Fee should be charged on whole second amount,
     * after discount has been exceeded
     */
    public function testNaturalPersonFeeWithoutDiscount(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $firstOperation = new Operation(
            date('Y-m-d'),
            6,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            1000.00,
            Operation::CURRENCY_EUR
        );

        $secondOperation = new Operation(
            date('Y-m-d'),
            6,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            600.00,
            Operation::CURRENCY_EUR
        );

        $cashOutProcessor->calculateFee($firstOperation);
        $fee = $cashOutProcessor->calculateFee($secondOperation);

        $expectedFee = ($secondOperation->getAmount() * CashOutProcessor::NATURAL_FEE_PERCENTAGE) / 100;

        $this->assertEquals($expectedFee, $fee);
    }

    /**
     * Test Natural Person Fee should be Free for next week Operation,
     * because Discount is per week and Amount is less than Discount.
     */
    public function testNaturalPersonDiscountNextWeek(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $firstOperation = new Operation(
            date('Y-m-d'),
            7,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            1000.00,
            Operation::CURRENCY_EUR
        );

        /**
         * Operation with next week Date
         */
        $secondOperation = new Operation(
            date('Y-m-d', time() + 7 * 24 * 60 * 60),
            7,
            Operation::USER_TYPE_NATURAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            700.00,
            Operation::CURRENCY_EUR
        );

        $cashOutProcessor->calculateFee($firstOperation);
        $fee = $cashOutProcessor->calculateFee($secondOperation);

        $this->assertEquals(0, $fee);
    }

    /**
     * Test Legal Person Fee Calculation
     */
    public function testCalculateLegalPersonFee(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            8,
            Operation::USER_TYPE_LEGAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            400.00,
            Operation::CURRENCY_EUR
        );

        $fee = $cashOutProcessor->calculateFee($operation);
        $expectedFee = ($operation->getAmount() * CashOutProcessor::LEGAL_FEE_PERCENTAGE) / 100;

        $this->assertEquals($expectedFee, $fee);
    }

    /**
     * Test Legal Person Fee for small amount should be Minimum Fee
     */
    public function testLegalPersonMinimumFee(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            9,
            Operation::USER_TYPE_LEGAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            2.00,
            Operation::CURRENCY_EUR
        );

        $fee = $cashOutProcessor->calculateFee($operation);

        $this->assertEquals(CashOutProcessor::LEGAL_MINIMUM_FEE['amount'], $fee);
    }

    /**
     * Test Minimum Legal Person Fee calculation based on Currency conversion rate
     */
    public function testMinimumFeeInOtherCurrency(): void
    {
        $cashOutProcessor = new CashOutProcessor();

        $operation = new Operation(
            date('Y-m-d'),
            10,
            Operation::USER_TYPE_LEGAL,
            Operation::OPERATION_TYPE_CASH_OUT,
            1.00,
            Operation::CURRENCY_JPY
        );

        $minimumFee = $cashOutProcessor->getLegalPersonMinimumFee($operation);
        $expectedMinimumFee = CashOutProcessor::LEGAL_MINIMUM_FEE['amount'] * CommissionProcessor::CONVERSION_RATES[$operation->getCurrency()];

        $this->assertEquals($expectedMinimumFee, $minimumFee);
    }
}