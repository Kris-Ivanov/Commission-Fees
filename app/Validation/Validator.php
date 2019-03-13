<?php

namespace App\Validation;

use App\Operation;

class Validator
{
    /**
     * Validate Operation data array
     *
     * @param array $data
     * @return bool
     */
    public static function validateOperationData(array $data): bool
    {
        if (count($data) < 6) {
            return false;
        }

        $date = self::validateDate($data[0]);
        $userType = self::validateUserType($data[2]);
        $operationType = self::validateOperationType($data[3]);
        $amount = self::validateAmount($data[4]);
        $currency = self::validateCurrency($data[5]);

        if (!$date || !$userType || !$operationType || !$amount || !$currency) {
            return false;
        }

        return true;
    }

    /**
     * Check if Date is valid and in the correct format
     *
     * @param string $date
     *
     * @return bool
     */
    private static function validateDate(string $date): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);

        if (!$date) {
            return false;
        }

        return true;
    }

    /**
     * Check if User Type is one of the allowed user types
     *
     * @param string $userType
     *
     * @return bool
     */
    private static function validateUserType(string $userType): bool
    {
        return in_array($userType, Operation::USER_TYPES);
    }

    /**
     * Check if Operation Type is one of the allowed operation types
     *
     * @param string $operationType
     *
     * @return bool
     */
    private static function validateOperationType(string $operationType): bool
    {
        return in_array($operationType, Operation::OPERATION_TYPES);
    }

    /**
     * Check if amount is a float number
     *
     * @param $amount
     *
     * @return bool
     */
    private static function validateAmount($amount): bool
    {
        $floatAmount = (float) $amount;

        return $floatAmount > 0;
    }

    /**
     * Check if currency is one of the allowed currencies
     *
     * @param string $currency
     *
     * @return bool
     */
    private static function validateCurrency(string $currency): bool
    {
        return in_array($currency, Operation::CURRENCIES);
    }
}