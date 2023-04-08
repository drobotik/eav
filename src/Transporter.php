<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Exception;

class Transporter
{
    private array $data = [];

    public function setField($field, $value) : self
    {
        $this->data[$field] = $value;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getField($field)
    {
        if(!$this->hasField($field))
            return null;

        return $this->data[$field];
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function setData(array $data) : self
    {
        $this->data = $data;
        return $this;
    }

    public function hasField($field) : bool
    {
        return array_key_exists($field, $this->data);
    }

    public function removeField($field) : void
    {
        unset($this->data[$field]);
    }

    public function clear() : void
    {
        $this->data = [];
    }

    public function __get(string $field)
    {
        return $this->getField($field);
    }

    public function __set(string $field, $value) : void
    {
        $this->setField($field, $value);
    }

    public function __isset(string $field) : bool
    {
        return $this->hasField($field);
    }

    public function __unset(string $field) : void
    {
        $this->removeField($field);
    }

    public function __toString() : string
    {
        return json_encode($this->data);
    }

    public function __toArray() : array
    {
        return $this->data;
    }

    public function __toJson() : string
    {
        return json_encode($this->data);
    }

    public function __toObject() : object
    {
        return (object) $this->data;
    }
}