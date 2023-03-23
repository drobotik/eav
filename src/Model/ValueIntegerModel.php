<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;

class ValueIntegerModel extends ValueBase
{
    public function __construct(array $attributes = [])
    {
        $this->table = ATTR_TYPE::INTEGER->valueTable();
        parent::__construct($attributes);
    }
}