<?php

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_PIVOT;

class PivotModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _PIVOT::table();
        $this->primaryKey = _PIVOT::ID->column();
        $this->fillable = [
            _PIVOT::DOMAIN_ID->column(),
            _PIVOT::SET_ID->column(),
            _PIVOT::GROUP_ID->column(),
            _PIVOT::ATTR_ID->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getDomainKey()
    {
        return $this->{_PIVOT::DOMAIN_ID->column()};
    }

    public function setDomainKey($key) : self
    {
        $this->{_PIVOT::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function getSetKey()
    {
        return $this->{_PIVOT::SET_ID->column()};
    }

    public function setSetKey($key) : self
    {
        $this->{_PIVOT::SET_ID->column()} = $key;
        return $this;
    }

    public function getGroupKey()
    {
        return $this->{_PIVOT::GROUP_ID->column()};
    }

    public function setGroupKey($key) : self
    {
        $this->{_PIVOT::GROUP_ID->column()} = $key;
        return $this;
    }

    public function getAttrKey()
    {
        return $this->{_PIVOT::ATTR_ID->column()};
    }

    public function setAttrKey($key) : self
    {
        $this->{_PIVOT::ATTR_ID->column()} = $key;
        return $this;
    }
}