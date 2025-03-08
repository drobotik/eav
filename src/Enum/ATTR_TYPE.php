<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Validation\Assert;
use Faker\Factory;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints;

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


    public static function doctrineType(string $name): string
    {
        switch ($name) {
            case self::INTEGER:
                return Types::INTEGER;
            case self::DATETIME:
                return Types::DATETIME_MUTABLE;
            case self::DECIMAL:
                return Types::DECIMAL;
            case self::STRING:
                return Types::STRING;
            case self::TEXT:
                return Types::TEXT;
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    public static function migrateOptions(string $name) : array
    {
        switch ($name) {
            case self::DATETIME:
            case self::STRING:
            case self::TEXT:
            case self::INTEGER:
                return [];
            case self::DECIMAL:
                return [
                    'precision' => 21,
                    'scale' => 6
                ];
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    public static function validationRule(string $name): array
    {
        switch ($name) {
            case self::INTEGER:
                return [
                    Assert::integer()
                ];
            case self::DATETIME:
                return [
                    new Constraints\Date
                ];
            case self::DECIMAL:
                return [
                    new Constraints\Regex('/^[0-9]{1,11}(?:\.[0-9]{1,3})?$/')
                ];
            case self::STRING:
                return [
                    new Constraints\Length([
                        'min' => 1,
                        'max' => 191
                    ])
                ];
            case self::TEXT:
                return [
                    new Constraints\Length([
                        'min' => 1,
                        'max' => 10000
                    ])
                ];
            default:
                throw new InvalidArgumentException("Invalid type: " . $name);
        }
    }

    public static function randomValue(string $name, $iterator = null): mixed
    {
        $faker = Factory::create();
        switch ($name) {
            case self::INTEGER:
                return $faker->randomNumber();
            case self::DATETIME:
                return Carbon::now()->subDays($iterator)->format('Y-m-d H:i:s');
            case self::DECIMAL:
                return $faker->randomFloat(3);
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
