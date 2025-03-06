<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Result;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Model\ValueBase;

class EntityFactoryResult
{
    private int $entityKey;
    private array $pivots = [];
    private array $attributes = [];
    /** @var ValueBase[] */
    private array $values = [];

    private int $domainKey;
    private int $setKey;


    public function setDomainKey(int $key) : void
    {
        $this->domainKey = $key;
    }

    public function setSetKey(int $key) : void
    {
        $this->setKey = $key;
    }

    public function getSetKey() : int
    {
        return $this->setKey;
    }

    public function getDomainKey() : int
    {
        return $this->domainKey;
    }

    public function setEntityKey(int $entityKey): void
    {
        $this->entityKey = $entityKey;
    }

    public function getEntityKey(): int
    {
        return $this->entityKey;
    }

    public function addAttribute(array $attribute): void
    {
        $this->attributes[$attribute[_ATTR::NAME]] = $attribute;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addValue(int $attrKey, int $valueKey): void
    {
        $this->values[$attrKey] = $valueKey;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function addPivot(int $attrKey, int $pivotKey): void
    {
        $this->pivots[$attrKey] = $pivotKey;
    }

    public function getPivots() : array
    {
        return $this->pivots;
    }
}