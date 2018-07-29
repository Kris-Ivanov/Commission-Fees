<?php

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
        $input = [
            '2016-01-07,1,natural,cash_out,100.00,USD',
            '2016-01-06,1,natural,cash_out,30000,JPY',
            '2016-02-15,1,natural,cash_out,300.00,EUR',
        ];

        $commissions = \App\Processor\InputProcessor::processInput($input);

        $this->assertCount(count($input), $commissions);
    }
}