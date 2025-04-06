<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Value;

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
