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

    public function default() : string|bool|null|ATTR_TYPE
    {
        return match ($this) {
            self::ID,
            self::NAME,
            self::DOMAIN_ID,
            self::SOURCE,
            self::DEFAULT_VALUE,
            self::DESCRIPTION=> null,
            self::TYPE => ATTR_TYPE::STRING,
            self::STRATEGY => Strategy::class
        };
    }

    public static function bag() : array
    {
        $output = [];
        foreach (self::cases() as $case) {
            $output[$case->column()] = $case->default();
        }
        return $output;
    }
}
