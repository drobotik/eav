<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_VALUE;

class Value extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->primaryKey = _VALUE::ID->column();
        $this->fillable = [
            _VALUE::DOMAIN_ID->column(),
            _VALUE::ENTITY_ID->column(),
            _VALUE::ATTRIBUTE_ID->column(),
            _VALUE::VALUE->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }
    public function getDomainKey()
    {
        return $this->{_VALUE::DOMAIN_ID->column()};
    }

    public function setDomainKey($key) : self
    {
        $this->{_VALUE::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function getEntityKey()
    {
        return $this->{_VALUE::ENTITY_ID->column()};
    }

    public function setEntityKey($key) : self
    {
        $this->{_VALUE::ENTITY_ID->column()} = $key;
        return $this;
    }

    public function getAttrKey()
    {
        return $this->{_VALUE::ATTRIBUTE_ID->column()};
    }

    public function setAttrKey($key) : self
    {
        $this->{_VALUE::ATTRIBUTE_ID->column()} = $key;
        return $this;
    }

    public function getVal()
    {
        return $this->{_VALUE::VALUE->column()};
    }

    public function setVal($value) : self
    {
        $this->{_VALUE::VALUE->column()} = $value;
        return $this;
    }
}