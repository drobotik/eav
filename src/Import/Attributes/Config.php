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

    public function appendAttribute(ConfigAttribute $attribute): void
    {
        $attribute->validate();
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
}