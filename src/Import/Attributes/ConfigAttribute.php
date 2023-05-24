<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;

class ConfigAttribute
{
    private int $groupKey;
    private array $fields;

    public function getKey() : ?int
    {
        return isset($this->fields[_ATTR::ID->column()])
            ? $this->fields[_ATTR::ID->column()]
            : null;
    }

    public function getName() : string
    {
        return $this->fields[_ATTR::NAME->column()];
    }

    public function getType() : ATTR_TYPE
    {
        return ATTR_TYPE::getCase($this->fields[_ATTR::TYPE->column()]);
    }

    public function getGroupKey() : int
    {
        return $this->groupKey;
    }

    public function setGroupKey(int $key) : void
    {
        $this->groupKey = $key;
    }


    public function setFields(array $config) : void
    {
        $this->fields = $config;
    }

    public function getFields() : array
    {
        return $this->fields;
    }
}