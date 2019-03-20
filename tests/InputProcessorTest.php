<?php

use App\Processor\CashInProcessor;
use App\Processor\CashOutProcessor;
use App\Processor\CommissionProcessor;
use App\Processor\InputProcessor;
use App\Validation\Validator;
use PHPUnit\Framework\TestCase;

class InputProcessorTest extends TestCase
{
    /**
     * Test process input function,
     * it should return the same number of commission fees
     * as the number of input lines
     */
    public function testProcessInput(): void
    {
        $validator = new Validator();
        $cashInProcessor = new CashInProcessor();
        $cashOutProcessor = new CashOutProcessor();
        $commissionProcessor = new CommissionProcessor($cashInProcessor, $cashOutProcessor);
        $inputProcessor = new InputProcessor($validator, $commissionProcessor);

        $input = [
            '2016-01-07,1,natural,cash_out,100.00,USD',
            '2016-01-06,1,natural,cash_out,30000,JPY',
            '2016-02-15,1,natural,cash_out,300.00,EUR',
        ];

        $commissions = $inputProcessor->processInput($input);

        $this->assertCount(count($input), $commissions);
    }
}