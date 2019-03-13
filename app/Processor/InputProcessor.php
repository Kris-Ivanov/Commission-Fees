<?php

namespace App\Processor;

use App\Operation;
use App\Validation\Validator;

class InputProcessor
{
    private static $commissions = [];

    /**
     * Process Input lines and calculate Commission Fees
     *
     * @param array $input
     *
     * @return array $commissions
     */
    public static function processInput(array $input): array
    {
        foreach ($input as $line) {
            $operationData = explode(',', $line);

            $isValid = Validator::validateOperationData($operationData);
            if (!$isValid) {
                die("Invalid Input! \n");
            }

            $operation = new Operation(
                $operationData[0],
                $operationData[1],
                $operationData[2],
                $operationData[3],
                $operationData[4],
                $operationData[5]
            );

            self::$commissions[] = CommissionProcessor::calculateCommission($operation);
        }

        return self::$commissions;
    }
}