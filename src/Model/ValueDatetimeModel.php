<?php

namespace Kuperwood\Eav\Model;

use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;

class ValueDatetimeModel extends Value
{
    public function __construct(array $attributes = [])
    {
        $this->table = sprintf(_VALUE::table(), ATTR_TYPE::DATETIME->value());
        parent::__construct($attributes);
    }
}