<?php

namespace App;

class Operation
{
    const USER_TYPE_NATURAL = 'natural';
    const USER_TYPE_LEGAL = 'legal';

    const USER_TYPES = [
        self::USER_TYPE_NATURAL,
        self::USER_TYPE_LEGAL,
    ];

    const OPERATION_TYPE_CASH_IN = 'cash_in';
    const OPERATION_TYPE_CASH_OUT = 'cash_out';

    const OPERATION_TYPES = [
        self::OPERATION_TYPE_CASH_IN,
        self::OPERATION_TYPE_CASH_OUT,
    ];

    const CURRENCY_EUR = 'EUR';
    const CURRENCY_USD = 'USD';
    const CURRENCY_JPY = 'JPY';

    const CURRENCIES = [
        self::CURRENCY_EUR,
        self::CURRENCY_USD,
        self::CURRENCY_JPY,
    ];

    private $date;

    private $userId;

    private $userType;

    private $operationType;

    private $amount;

    private $currency;

    /**
     * Operation constructor.
     *
     * @param string $date
     * @param int $userId
     * @param string $userType
     * @param string $operationType
     * @param float $amount
     * @param string $currency
     */
    public function __construct(string $date, int $userId, string $userType, string $operationType, float $amount, string $currency)
    {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUserType()
    {
        return $this->userType;
    }

    public function getOperationType()
    {
        return $this->operationType;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}