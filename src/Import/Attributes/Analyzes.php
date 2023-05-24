<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

class Analyzes
{
    private array $attributes = [];
    private array $pivots     = [];

    public function appendAttribute(string $name): void
    {
        $this->attributes[$name] = $name;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function isAttributes() : bool
    {
        return count($this->attributes) > 0;
    }

    public function appendPivot(int $key, string $name) : void
    {
        $this->pivots[$key] = $name;
    }

    public function getPivots() : array
    {
        return $this->pivots;
    }

    public function isPivots() : bool
    {
        return count($this->pivots) > 0;
    }

}