<?php

declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;

class ValueStringModel extends ValueBase
{
    public function __construct(array $attributes = [])
    {
        $this->table = ATTR_TYPE::STRING->valueTable();
        parent::__construct($attributes);
    }
}