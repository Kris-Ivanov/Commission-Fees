<?php

namespace App;

class Registry
{
    private static $userOperationsData = [];

    /**
     * Private Registry constructor in order to prevent creation of new instances.
     */
    private function __construct()
    {

    }

    /**
     * Set User Operations Data
     *
     * @param int $key
     * @param array $value
     */
    public static function setUserOperationsData(int $key, array $value): void
    {
        self::$userOperationsData[$key] = $value;
    }

    /**
     * Get User Operations Data
     *
     * @param int $key
     *
     * @return array
     */
    public static function getUserOperationsData(int $key): array
    {
        if (!isset(self::$userOperationsData[$key])) {
            self::$userOperationsData[$key] = [];
        }

        return self::$userOperationsData[$key];
    }
}