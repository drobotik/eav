<?php

namespace Drobotik\Eav\Enum;

enum ATTR_FACTORY
{
    case ATTRIBUTE;
    case GROUP;
    case VALUE;

    public function field(): string
    {
        return match ($this) {
            self::ATTRIBUTE => "attribute",
            self::GROUP => "group",
            self::VALUE => "value"
        };
    }
}