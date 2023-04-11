<?php

declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;

class AttributeModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _ATTR::table();
        $this->primaryKey = _ATTR::ID->column();
        $this->fillable = [
            _ATTR::NAME->column(),
            _ATTR::DOMAIN_ID->column(),
            _ATTR::TYPE->column(),
            _ATTR::DESCRIPTION->column(),
            _ATTR::DEFAULT_VALUE->column(),
            _ATTR::SOURCE->column(),
            _ATTR::STRATEGY->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(DomainModel::class, _ATTR::DOMAIN_ID->column(), _DOMAIN::ID->column());
    }

    public function getDomainKey()
    {
        return $this->{_ATTR::DOMAIN_ID->column()};
    }

    public function setDomainKey(int $key) : self
    {
        $this->{_ATTR::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function getName()
    {
        return $this->{_ATTR::NAME->column()};
    }

    public function setName(string $name) : self
    {
        $this->{_ATTR::NAME->column()} = $name;
        return $this;
    }

    public function getType()
    {
        return $this->{_ATTR::TYPE->column()};
    }

    public function setType(string $type) : self
    {
        $this->{_ATTR::TYPE->column()} = $type;
        return $this;
    }

    public function getDescription()
    {
        return $this->{_ATTR::DESCRIPTION->column()};
    }

    public function setDescription(?string $description) : self
    {
        $this->{_ATTR::DESCRIPTION->column()} = $description;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->{_ATTR::DEFAULT_VALUE->column()};
    }

    public function setDefaultValue($value) : self
    {
        $this->{_ATTR::DEFAULT_VALUE->column()} = $value;
        return $this;
    }

    public function getSource()
    {
        return $this->{_ATTR::SOURCE->column()};
    }

    public function setSource(?string $source) : self
    {
        $this->{_ATTR::SOURCE->column()} = $source;
        return $this;
    }

    public function getStrategy()
    {
        return $this->{_ATTR::STRATEGY->column()};
    }

    public function setStrategy(string $strategy) : self
    {
        $this->{_ATTR::STRATEGY->column()} = $strategy;
        return $this;
    }
}
