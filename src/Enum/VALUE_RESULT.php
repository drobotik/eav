<?php

namespace Kuperwood\Eav\Enum;

enum VALUE_RESULT
{
    case CREATED;
    case UPDATED;
    case NOT_ALLOWED;
    case EMPTY;

    public function code(): int
    {
        return match ($this) {
            self::CREATED => 1,
            self::UPDATED => 2,
            self::NOT_ALLOWED => 3,
            self::EMPTY => 4,

        };
    }

    public function message(): string
    {
        return match ($this) {
            self::NOT_ALLOWED => "Creating is not allowed by attribute setting",
            self::EMPTY => "Nothing to perform",
            self::CREATED => "Created",
            self::UPDATED => "Updated",
        };
    }
}