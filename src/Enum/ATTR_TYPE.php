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
use Drobotik\Eav\Exception\AttributeTypeException;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;

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

    public function model(): ValueBase
    {
        return match ($this) {
            self::INTEGER => new ValueIntegerModel,
            self::DATETIME => new ValueDatetimeModel,
            self::DECIMAL => new ValueDecimalModel,
            self::STRING => new ValueStringModel,
            self::TEXT => new ValueTextModel
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

    public function validationRule() {
        return match ($this) {
            self::INTEGER => [
                "integer"
            ],
            self::DATETIME => [
                "date"
            ],
            self::DECIMAL => [
                "regex:/^[0-9]{1,11}(?:\.[0-9]{1,3})?$/"
            ],
            self::STRING => [
                "string",
                "min:1",
                "max:191"
            ],
            self::TEXT => [
                "min:1",
                "max:1000"
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
     * @throws AttributeTypeException
     */
    public static function getCase(string $type): ATTR_TYPE
    {
        return match ($type) {
            self::INTEGER->value() => self::INTEGER,
            self::DATETIME->value() => self::DATETIME,
            self::DECIMAL->value() => self::DECIMAL,
            self::STRING->value() => self::STRING,
            self::TEXT->value() => self::TEXT,
            default => AttributeTypeException::unsupportedType()
        };
    }
}
