<?php

namespace Kuperwood\Eav\Value;

/**
 * @property mixed $data
 */
class ValueState
{
    protected mixed $value;

    public function __construct() {
        $this->clear();
    }

    public function set(mixed $value) : void
    {
        $this->value = $value;
    }

    public function get() : mixed
    {
        if(!$this->isChanged())
            return null;

        return $this->value;
    }

    public function isChanged() : bool
    {
        return !$this->value instanceof EmptyValue;
    }

    public function clear() : void
    {
        $this->value = new EmptyValue();
    }
}
