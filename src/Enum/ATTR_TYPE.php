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
use Symfony\Component\Validator\Constraints;

enum ATTR_TYPE
{
    case INTEGER;
    case DATETIME;
    case DECIMAL;
    case STRING;
    case TEXT;
    case MANUAL;

    public function value(): string
    {
        return match ($this) {
            self::INTEGER => "int",
            self::DATETIME => "datetime",
            self::DECIMAL => "decimal",
            self::STRING => "varchar",
            self::TEXT => "text",
            self::MANUAL => "manual"
        };
    }

    public static function isValid(string $type): bool
    {
        if($type === self::MANUAL->value()) return false;
        $cases = array_map(fn($case) => $case->value(), self::cases());
        return in_array($type, $cases);
    }

    public function valueTable(): string
    {
        return match ($this) {
            self::INTEGER => sprintf(_VALUE::table(), self::INTEGER->value()),
            self::DATETIME => sprintf(_VALUE::table(), self::DATETIME->value()),
            self::DECIMAL => sprintf(_VALUE::table(), self::DECIMAL->value()),
            self::STRING => sprintf(_VALUE::table(), self::STRING->value()),
            self::TEXT => sprintf(_VALUE::table(), self::TEXT->value())
        };
    }

    public function doctrineType() : string
    {
        return match ($this) {
            self::DATETIME => Types::DATETIME_MUTABLE,
            self::DECIMAL => Types::DECIMAL,
            self::INTEGER => Types::INTEGER,
            self::TEXT => Types::TEXT,
            self::STRING => Types::STRING,
        };
    }

    public function migrateOptions() : array
    {
        return match ($this) {
            self::DATETIME => [],
            self::DECIMAL => [
                'precision' => 21,
                'scale' => 6
            ],
            self::INTEGER => [],
            self::TEXT => [],
            self::STRING => []
        };
    }

    public function validationRule() {
        return match ($this) {
            self::INTEGER => [
                Assert::integer()
            ],
            self::DATETIME => [
                new Constraints\Date
            ],
            self::DECIMAL => [
                new Constraints\Regex('/^[0-9]{1,11}(?:\.[0-9]{1,3})?$/')
            ],
            self::STRING => [
                new Constraints\Length([
                    'min' => 1,
                    'max' => 191
                ])
            ],
            self::TEXT => [
                new Constraints\Length([
                    'min' => 1,
                    'max' => 10000
                ])
            ]
        };
    }

    public function randomValue($iterator = null) {
        $faker = \Faker\Factory::create();
        return match ($this) {
            self::STRING =>  $faker->words(2, true),
            self::INTEGER => $faker->randomNumber(),
            self::DECIMAL => $faker->randomFloat(3),
            self::DATETIME => Carbon::now()->subDays($iterator)->format('Y-m-d H:i:s'),
            self::TEXT => $faker->text(150),
        };
    }

    /**
     * @throws AttributeException
     */
    public static function getCase(string $type): ATTR_TYPE
    {
        return match ($type) {
            self::INTEGER->value() => self::INTEGER,
            self::DATETIME->value() => self::DATETIME,
            self::DECIMAL->value() => self::DECIMAL,
            self::STRING->value() => self::STRING,
            self::TEXT->value() => self::TEXT,
            default => AttributeException::unsupportedType($type)
        };
    }
}
