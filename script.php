<?php

require __DIR__ . '/vendor/autoload.php';

use App\Processor\InputProcessor;

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
$commissions = InputProcessor::processInput($inputArray);

/**
 * Print Commission Fees
 */
foreach ($commissions as $commission) {
    fwrite(STDOUT, $commission . "\n");
}