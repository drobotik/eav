<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_GROUP;

class GroupModel extends Model
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

    public function getSetKey()
    {
        return $this->{_GROUP::SET_ID->column()};
    }

    public function setSetKey($key) : self
    {
        $this->{_GROUP::SET_ID->column()} = $key;
        return $this;
    }

    public function getName()
    {
        return $this->{_GROUP::NAME->column()};
    }

    public function setName($name) : self
    {
        $this->{_GROUP::NAME->column()} = $name;
        return $this;
    }
}