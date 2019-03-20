<?php

require __DIR__ . '/vendor/autoload.php';

use App\Processor\CashInProcessor;
use App\Processor\CashOutProcessor;
use App\Processor\CommissionProcessor;
use App\Processor\InputProcessor;
use App\Validation\Validator;

$arguments = $argv;

if (!isset($arguments[1])) {
    die("Missing Input File Path! \n");
}

/**
 * Read Input File
 */
$inputFile = $arguments[1];

$input = file_get_contents($inputFile);
$inputArray = explode("\n", $input);

/**
 * Process Operations
 */
$validator = new Validator();
$cashInProcessor = new CashInProcessor();
$cashOutProcessor = new CashOutProcessor();
$commissionProcessor = new CommissionProcessor($cashInProcessor, $cashOutProcessor);
$inputProcessor = new InputProcessor($validator, $commissionProcessor);

$inputProcessor->processInput($inputArray);
