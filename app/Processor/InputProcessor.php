<?php

namespace App\Processor;

use App\Operation;
use App\Validation\Validator;

class InputProcessor
{
    /**
     * @var Validator $validator
     */
    private $validator;

    /**
     * @var CommissionProcessor $commissionProcessor
     */
    private $commissionProcessor;

    /**
     * InputProcessor constructor.
     * @param Validator $validator
     * @param CommissionProcessor $commissionProcessor
     */
    public function __construct(Validator $validator, CommissionProcessor $commissionProcessor)
    {
        $this->validator = $validator;
        $this->commissionProcessor = $commissionProcessor;
    }

    /**
     * Process Input lines and calculate Commission Fees
     *
     * @param array $input
     *
     * @return void
     */
    public function processInput(array $input): void
    {
        foreach ($input as $line) {
            $operationData = explode(',', $line);

            $isValid = $this->validator->validateOperationData($operationData);
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

            $commission = $this->commissionProcessor->calculateCommission($operation);

            fwrite(STDOUT, $commission . "\n");
        }
    }
}