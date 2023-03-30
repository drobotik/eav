<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Exception\AttributeSetException;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Trait\EavContainerTrait;

class AttributeSet
{
    use EavContainerTrait;

    private int $key;
    private string $name;
    private array $attributes = [];
    private Entity $entity;

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key) : self
    {
        $this->key = $key;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function push(Attribute $attribute) : self
    {
        $this->attributes[$attribute->getName()] = $attribute;
        return $this;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * @throws AttributeSetException
     */
    public function getAttribute(string $name) : Attribute
    {
        if(!$this->hasAttribute($name))
            AttributeSetException::undefinedAttribute($name);
        return $this->attributes[$name];
    }

    public function hasAttribute(string $name) : bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function reset() : self
    {
        $this->attributes = [];
        return $this;
    }

    public function fetch() : self
    {
        $set = AttributeSetModel::where(_SET::ID->column(), "=", $this->getKey())->firstOrFail();
        $attributes = $set->attributes()->get();
        /** @var AttributeModel $attr */
        foreach ($attributes as $attr) {
            $instance = new Attribute();
            $instance->getBag()->setFields($attr->makeHidden('pivot')->toArray());
            $this->push($instance);
        }
        return $this;
    }
}