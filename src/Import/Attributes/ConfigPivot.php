<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

class ConfigPivot
{

    private int $attributeKey;
    private int $groupKey;
    public function getGroupKey() : int
    {
        return $this->groupKey;
    }

    public function setGroupKey(int $key) : void
    {
        $this->groupKey = $key;
    }

    public function getAttributeKey() : int
    {
        return $this->attributeKey;
    }

    public function setAttributeKey(int $key) : void
    {
        $this->attributeKey = $key;
    }
}