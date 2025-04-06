<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use DateTime;

use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Validation\Constraints\DateConstraint;
use Drobotik\Eav\Validation\Constraints\LengthConstraint;
use Drobotik\Eav\Validation\Constraints\NumericConstraint;
use Drobotik\Eav\Validation\Constraints\RegexConstraint;
use Faker\Factory;
use InvalidArgumentException;

class ATTR_TYPE
{

    public const INTEGER = "int";
    public const DATETIME = "datetime";
    public const DECIMAL = "decimal";
    public const STRING = "varchar";
    public const TEXT = "text";
    public const MANUAL = "manual";

    private static function getCases(): array
    {
        return [
            self::INTEGER,
            self::DATETIME,
            self::DECIMAL,
            self::STRING,
            self::TEXT
        ];
    }

    public static function isValid(string $type): bool
    {
        if ($type === self::MANUAL) {
            return false;
        }

        return in_array($type, self::getCases());
    }

    public static function valueTable(string $name): string
    {
        switch ($name) {
            case self::INTEGER:
                return sprintf(_VALUE::table(), self::INTEGER);
            case self::DATETIME:
                return sprintf(_VALUE::table(), self::DATETIME);
            case self::DECIMAL:
                return sprintf(_VALUE::table(), self::DECIMAL);
            case self::STRING:
                return sprintf(_VALUE::table(), self::STRING);
            case self::TEXT:
                return sprintf(_VALUE::table(), self::TEXT);
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    public static function validationRule(string $name): array
    {
        switch ($name) {
            case self::INTEGER:
                return [
                    new NumericConstraint()
                ];
            case self::DATETIME:
                return [
                    new DateConstraint('Y-m-d H:i:s')
                ];
            case self::DECIMAL:
                return [
                    new RegexConstraint('/^[0-9]{1,11}(?:\.[0-9]{1,3})?$/')
                ];
            case self::STRING:
                return [
                    new LengthConstraint(1, 191)
                ];
            case self::TEXT:
                return [
                    new LengthConstraint(1, 10000)
                ];
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    public static function randomValue(string $name, $iterator = null)
    {
        $faker = Factory::create();
        switch ($name) {
            case self::INTEGER:
                return $faker->randomNumber();
            case self::DATETIME:
                return (new DateTime())->setTimestamp(rand(strtotime('1980-01-01 00:00:00'), strtotime('2025-12-31 23:59:59')))->format('Y-m-d H:i:s.u');
            case self::DECIMAL:
                return $faker->randomFloat(6);
            case self::STRING:
                return $faker->words(2, true);
            case self::TEXT:
                return $faker->text(150);
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    /**
     * @throws AttributeException
     */
    public static function getCase(string $type)
    {
        switch ($type) {
            case self::INTEGER:
                return self::INTEGER;
            case self::DATETIME:
                return self::DATETIME;
            case self::DECIMAL:
                return self::DECIMAL;
            case self::STRING:
                return self::STRING;
            case self::TEXT:
                return self::TEXT;
            default:
                throw AttributeException::unsupportedType($type);
        }
    }
}
