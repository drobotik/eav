<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Value;

/**
 * @property $data
 */
class ValueState
{
    protected $value;

    public function __construct() {
        $this->clear();
    }

    public function set($value) : void
    {
        $this->value = $value;
    }

    public function get()
    {
        if(!$this->isChanged())
            return null;

        return $this->value;
    }

    public function isChanged() : bool
    {
        return !$this->value instanceof ValueEmpty;
    }

    public function clear() : void
    {
        $this->value = new ValueEmpty();
    }
}
