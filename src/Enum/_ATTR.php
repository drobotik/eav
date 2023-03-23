<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;
use Kuperwood\Eav\Strategy;

enum _ATTR implements DefineTableInterface
{
    CASE ID;
    CASE DOMAIN_ID;
    case NAME;
    case TYPE;
    case STRATEGY;
    case SOURCE;
    case DEFAULT_VALUE;
    case DESCRIPTION;

    public static function table() : string
    {
        return 'eav_attributes';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'attribute_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
            self::NAME => "name",
            self::TYPE => "type",
            self::STRATEGY => "strategy",
            self::SOURCE => "source",
            self::DEFAULT_VALUE => "default_value",
            self::DESCRIPTION => "description",
        };
    }

    public function default() : string|bool|null
    {
        return match ($this) {
            self::ID,
            self::NAME,
            self::DOMAIN_ID,
            self::SOURCE,
            self::DEFAULT_VALUE => null,
            self::TYPE => ATTR_TYPE::STRING->value(),
            self::STRATEGY => Strategy::class,
            self::DESCRIPTION => false,
        };
    }
}
