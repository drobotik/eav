<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Enum\_ATTR;

class AttributeBag
{
    private array $data;

    public function __construct()
    {
        $this->data = _ATTR::bag();
    }

    public function setField($field, $value): self
    {
        $this->data[$field] = $value;

        return $this;
    }

    public function getField($field)
    {
        return $this->data[$field];
    }

    public function resetField($field): self
    {
        $this->data[$field] = _ATTR::bag($field);

        return $this;
    }

    public function getFields(): array
    {
        return $this->data;
    }

    public function setFields(array $fields): self
    {
        $this->data = $fields;

        return $this;
    }
}
