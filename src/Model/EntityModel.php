<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Drobotik\Eav\Enum\_ENTITY;

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

    public function findAndDelete(int $key) : bool
    {
        return (bool) $this->query()->whereKey($key)->delete();
    }
}