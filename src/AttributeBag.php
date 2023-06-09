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

    public function setField(_ATTR $field, $value): self
    {
        $this->data[$field->column()] = $value;

        return $this;
    }

    public function getField(_ATTR $field)
    {
        return $this->data[$field->column()];
    }

    public function resetField(_ATTR $field): self
    {
        $this->data[$field->column()] = $field->default();

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
