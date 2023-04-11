<?php

declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_SET;

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

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeModel::class,
            _PIVOT::table(),
            _PIVOT::SET_ID->column(),
            _PIVOT::ATTR_ID->column()
        );
    }

    public function firstOrFail(int $key) : AttributeSetModel
    {
        return $this
            ->where(_SET::ID->column(), "=", $key)
            ->firstOrFail();
    }

    public function findAttributes(int $key) : Collection
    {
        return $this->firstOrFail($key)->attributes()->get();
    }
}