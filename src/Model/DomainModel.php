<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_DOMAIN;

class DomainModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _DOMAIN::table();
        $this->primaryKey = _DOMAIN::ID->column();
        $this->fillable = [
            _DOMAIN::NAME->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getName()
    {
        return $this->{_DOMAIN::NAME->column()};
    }

    public function setName($name) : self
    {
        $this->{_DOMAIN::NAME->column()} = $name;
        return $this;
    }
}