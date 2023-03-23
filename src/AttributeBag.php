<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_ATTR;

class AttributeBag
{
    private array $data;

    public function __construct()
    {
        $this->data = array_map(fn(_ATTR $field) => $field->default(), _ATTR::cases());
    }

    public function setField(_ATTR $field, $value) : self
    {
        $this->data[$field->column()] = $value;
        return $this;
    }

    public function getField(_ATTR $field)
    {
        return $this->data[$field->column()];
    }

    public function resetField(_ATTR $field) : self
    {
        $this->data[$field->column()] = $field->default();
        return $this;
    }

    public function getFields() : array
    {
        return $this->data;
    }
}