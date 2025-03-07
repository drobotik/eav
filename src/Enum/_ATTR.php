<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Interface\DefineTableInterface;
use Drobotik\Eav\Strategy;

class _ATTR implements DefineTableInterface
{

    public const ID = 'attribute_id';
    public const DOMAIN_ID = 'domain_id';
    public const NAME = 'name';
    public const TYPE = 'type';
    public const STRATEGY = 'strategy';
    public const SOURCE = 'source';
    public const DEFAULT_VALUE = 'default_value';
    public const DESCRIPTION = 'description';

    public static function table() : string
    {
        return 'eav_attributes';
    }

    public static function bag(string $key = null)
    {
        $defaults = [
            self::ID => null,
            self::NAME => null,
            self::DOMAIN_ID => null,
            self::SOURCE => null,
            self::DEFAULT_VALUE => null,
            self::DESCRIPTION => null,
            self::TYPE => ATTR_TYPE::STRING->value(),
            self::STRATEGY => Strategy::class
        ];

        if (key_exists($key, $defaults)) {
            return $defaults[$key];
        }

        return $defaults;
    }
}
