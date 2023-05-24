<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

class Config
{
    /** @var ConfigAttribute[] $attributes */
    private array $attributes = [];
    /** @var ConfigPivot[] $pivots */
    private array $pivots = [];

    public function appendAttribute(ConfigAttribute $attribute)
    {
        $this->attributes[$attribute->getName()] = $attribute;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    public function hasAttribute(string $name): bool
    {
        return key_exists($name, $this->attributes);
    }

    public function appendPivot(ConfigPivot $pivot)
    {
        $this->pivots[$pivot->getAttributeKey()] = $pivot;
    }

    public function getPivots() : array
    {
        return $this->pivots;
    }

    public function hasPivot(int $key) : bool
    {
        return key_exists($key, $this->pivots);
    }
}