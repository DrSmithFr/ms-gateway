<?php

declare(strict_types=1);

namespace App\Enum;

use ReflectionClass;
use ReflectionException;

abstract class Enum
{
    /**
     * @var array
     */
    private static $constCacheArray;

    /**
     * Enum constructor.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
        // disallow Enum creation
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function getAll(): array
    {
        if (self::$constCacheArray === null) {
            self::$constCacheArray = [];
        }

        $calledClass = static::class;

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * @param string $name
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidName(string $name): bool
    {
        $constants = self::getAll();
        return array_key_exists($name, $constants);
    }

    /**
     * @param string $value
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidValue(string $value): bool
    {
        $values = array_values(self::getAll());
        return in_array($value, $values, true);
    }
}
