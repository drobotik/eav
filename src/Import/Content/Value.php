<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Content;

use Drobotik\Eav\Enum\ATTR_TYPE;

class Value
{
    private mixed $value;
    private int $attributeKey;
    private string $attributeName;
    private ?int $entityKey = null;
    private ATTR_TYPE $type;

    public function setType(ATTR_TYPE $type): void
    {
        $this->type = $type;
    }

    public function getType() : ATTR_TYPE
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getEntityKey() : ?int
    {
        return $this->entityKey;
    }

    public function setEntityKey(int $key): void
    {
        $this->entityKey = $key;
    }

    public function isEntityKey() : bool
    {
        return !is_null($this->entityKey);
    }

    public function getAttributeKey() : int
    {
        return $this->attributeKey;
    }

    public function setAttributeKey(int $key): void
    {
        $this->attributeKey = $key;
    }

    public function getAttributeName() : string
    {
        return $this->attributeName;
    }

    public function setAttributeName(string $name): void
    {
        $this->attributeName = $name;
    }
}