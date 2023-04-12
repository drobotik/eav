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
use Drobotik\Eav\Enum\_VALUE;

class ValueBase extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->primaryKey = _VALUE::ID->column();
        $this->fillable = [
            _VALUE::DOMAIN_ID->column(),
            _VALUE::ENTITY_ID->column(),
            _VALUE::ATTRIBUTE_ID->column(),
            _VALUE::VALUE->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }
    public function getDomainKey()
    {
        return $this->{_VALUE::DOMAIN_ID->column()};
    }

    public function setDomainKey(int $key) : self
    {
        $this->{_VALUE::DOMAIN_ID->column()} = $key;
        return $this;
    }

    public function getEntityKey()
    {
        return $this->{_VALUE::ENTITY_ID->column()};
    }

    public function setEntityKey(int $key) : self
    {
        $this->{_VALUE::ENTITY_ID->column()} = $key;
        return $this;
    }

    public function getAttrKey()
    {
        return $this->{_VALUE::ATTRIBUTE_ID->column()};
    }

    public function setAttrKey(int $key) : self
    {
        $this->{_VALUE::ATTRIBUTE_ID->column()} = $key;
        return $this;
    }

    public function getValue()
    {
        return $this->{_VALUE::VALUE->column()};
    }

    public function setValue($value) : self
    {
        $this->{_VALUE::VALUE->column()} = $value;
        return $this;
    }
}