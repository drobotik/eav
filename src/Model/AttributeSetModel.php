<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Kuperwood\Eav\Enum\_SET;

class AttributeSetModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _SET::table();
        $this->primaryKey = _SET::ID->column();
        $this->fillable = [
            _SET::DOMAIN_ID->column(),
            _SET::NAME->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getDomainKey()
    {
        return $this->{_SET::DOMAIN_ID->column()};
    }

    public function setDomainKey(int $key) : self
    {
        $this->{_SET::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function getName()
    {
        return $this->{_SET::NAME->column()};
    }

    public function setName(string $name) : self
    {
        $this->{_SET::NAME->column()} = $name;
        return $this;
    }
}