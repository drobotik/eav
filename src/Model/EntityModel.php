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
            _ENTITY::DOMAIN_ID->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getDomainKey()
    {
        return $this->{_ENTITY::DOMAIN_ID->column()};
    }

    public function setDomainKey($key) : self
    {
        $this->{_ENTITY::DOMAIN_ID->column()} = $key;
        return $this;
    }
}