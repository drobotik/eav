<?php

declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Drobotik\Eav\Enum\_GROUP;

class AttributeGroupModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _GROUP::table();
        $this->primaryKey = _GROUP::ID->column();
        $this->fillable = [
            _GROUP::SET_ID->column(),
            _GROUP::NAME->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getAttrSetKey()
    {
        return $this->{_GROUP::SET_ID->column()};
    }

    public function setAttrSetKey(int $key) : self
    {
        $this->{_GROUP::SET_ID->column()} = $key;
        return $this;
    }

    public function getName()
    {
        return $this->{_GROUP::NAME->column()};
    }

    public function setName(string $name) : self
    {
        $this->{_GROUP::NAME->column()} = $name;
        return $this;
    }
}