<?php

namespace App\Processor;

use App\Operation;
use App\Registry;
use DateTime;

class CashOutProcessor
{
    const NATURAL_FEE_PERCENTAGE = 0.3;
    const NATURAL_WEEKLY_FREE_AMOUNT = [
        'amount' => 1000.00,
        'currency' => Operation::CURRENCY_EUR,
    ];

    const LEGAL_FEE_PERCENTAGE = 0.3;
    const LEGAL_MINIMUM_FEE = [
        'amount' => 0.50,
        'currency' => Operation::CURRENCY_EUR,
    ];

    /**
     * Calculate Fee for given Operation
     *
     * @param Operation $operation
     *
     * @return float
     */
    public static function calculateFee(Operation $operation): float
    {
        if ($operation->getUserType() === Operation::USER_TYPE_NATURAL) {
            return self::calculateNaturalPersonFee($operation);
        }

        return self::calculateLegalPersonFee($operation);
    }

    /**
     * Calculate Natural Person Fee and save User data for operation
     *
     * @param Operation $operation
     *
     * @return float
     */
    private static function calculateNaturalPersonFee(Operation $operation): float
    {
        $userDataArray = self::getUserData($operation);
        $week = $userDataArray['week'];
        $userData = $userDataArray['data'][$week];

        /**
         * User has no discount left
         */
        if ($userData['operations_count'] >= 3 || $userData['free_amount'] <= 0) {
            return ($operation->getAmount() * self::NATURAL_FEE_PERCENTAGE) / 100;
        }

        $amounts = self::getOperationAndDiscountAmounts($operation, $userData['free_amount']);

        self::updateUserData($userDataArray['data'], $operation->getUserId(), $week, $amounts['operation']);

        /**
         * Operation is free
         */
        if ($amounts['discount'] >= $operation->getAmount()) {
            return 0;
        }

        $amount = $operation->getAmount() - $amounts['discount'];

        return ($amount * self::NATURAL_FEE_PERCENTAGE) / 100;
    }

    /**
     * Get User Data Operation date's week from registry and create default values if none exist
     *
     * @param Operation $operation
     *
     * @return array
     * @throws ?DateTime Exception
     */
    private static function getUserData(Operation $operation): array
    {
        /**
         * Get calendar Week number of Operation Date
         */
        $date = new DateTime($operation->getDate());
        $week = $date->format("Y-W");

        /**
         * Get User Data from registry
         */
        $userData = Registry::getUserOperationsData($operation->getUserId());

        /**
         * Create default values for week if none exist
         */
        if (!isset($userData[$week])) {
            $userData[$week] = [
                'operations_count' => 0,
                'free_amount' => self::NATURAL_WEEKLY_FREE_AMOUNT['amount'],
            ];
        }

        return [
            'data' => $userData,
            'week' => $week,
        ];
    }

    /**
     * Update User Data in registry
     *
     * @param array $userData
     * @param int $userId
     * @param string $week
     * @param float $amount
     *
     * @return void
     */
    private static function updateUserData(array $userData, int $userId, string $week, float $amount): void
    {
        $userData[$week]['operations_count']++;
        $userData[$week]['free_amount'] = $userData[$week]['free_amount'] - $amount;

        Registry::setUserOperationsData($userId, $userData);
    }

    /**
     * @param Operation $operation
     * @param float $freeAmount
     *
     * @return array
     */
    private static function getOperationAndDiscountAmounts(Operation $operation, float $freeAmount): array
    {
        $eurAmount = $operation->getAmount();

        /**
         * Get the user's Free Amount in his Currency and his Operation Amount in EUR
         */
        if ($operation->getCurrency() !== self::NATURAL_WEEKLY_FREE_AMOUNT['currency']) {
            $freeAmount = CommissionProcessor::convertAmount($operation->getCurrency(), $freeAmount);
            $eurAmount = CommissionProcessor::convertAmount($operation->getCurrency(), $eurAmount, true);
        }

        return [
            'discount' => $freeAmount,
            'operation' => $eurAmount,
        ];
    }

    /**
     * Calculate Legal Person Fee
     *
     * @param Operation $operation
     *
     * @return float
     */
    private static function calculateLegalPersonFee(Operation $operation): float
    {
        $minimumFee = self::getLegalPersonMinimumFee($operation);

        $fee = ($operation->getAmount() * self::LEGAL_FEE_PERCENTAGE) / 100;

        if ($fee < $minimumFee) {
            return $minimumFee;
        }

        return $fee;
    }

    /**
     * Get Minimum Fee for Legal Person in Operation's currency
     *
     * @param Operation $operation
     *
     * @return float
     */
    public static function getLegalPersonMinimumFee(Operation $operation): float
    {
        $currency = $operation->getCurrency();

        if ($currency === self::LEGAL_MINIMUM_FEE['currency']) {
            return self::LEGAL_MINIMUM_FEE['amount'];
        }

        return CommissionProcessor::convertAmount($currency, self::LEGAL_MINIMUM_FEE['amount']);
    }
}