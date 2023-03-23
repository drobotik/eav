<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;

class ValueDecimalModel extends Value
{
    public function __construct(array $attributes = [])
    {
        $this->table = sprintf(_VALUE::table(), ATTR_TYPE::DECIMAL->value());
        parent::__construct($attributes);
    }
}