<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Content;

class ValueSet
{

    /** @var Value[]  */
    private array $values = [];

    public function appendValue(Value $value): void
    {
        $this->values[] = $value;
    }

    public function getValues() : array
    {
        return $this->values;
    }

    public function resetValues() : void
    {
        $this->values = [];
    }

    public function forNewEntities() : array
    {
        return array_values(array_filter($this->values, fn(Value $value) => $value->isLineIndex()));
    }
    /** @return Value[] */
    public function forExistingEntities() : array
    {
        return array_values(array_filter($this->values, fn(Value $value) => $value->isEntityKey()));
    }

}