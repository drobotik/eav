<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_ENTITY;

class EntityModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _ENTITY::table();
        $this->primaryKey = _ENTITY::ID->column();
        $this->fillable = [
            _ENTITY::DOMAIN_ID->column(),
            _ENTITY::ATTR_SET_ID->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getDomainKey()
    {
        return $this->{_ENTITY::DOMAIN_ID->column()};
    }

    public function setDomainKey(int $key) : self
    {
        $this->{_ENTITY::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function setAttrSetKey(int $key) : self
    {
        $this->{_ENTITY::ATTR_SET_ID->column()} = $key;
        return $this;
    }

    public function getAttrSetKey()
    {
        return $this->{_ENTITY::ATTR_SET_ID->column()};
    }
}