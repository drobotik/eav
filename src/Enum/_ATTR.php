<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\TableEnumInterface;

enum _ATTR implements TableEnumInterface
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
}
