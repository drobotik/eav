<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Illuminate\Database\Eloquent\Collection;
use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Exception\AttributeSetException;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Trait\ContainerTrait;
use Kuperwood\Eav\Trait\SingletonsTrait;

class AttributeSet
{
    use SingletonsTrait;
    use ContainerTrait;

    private int $key;
    private string $name;
    /** @var AttributeContainer[] */
    private array $containers = [];
    private Entity $entity;

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity) : self
    {
        $this->entity = $entity;
        return $this;
    }

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

    public function push(AttributeContainer $container) : self
    {
        $this->containers[$container->getAttribute()->getName()] = $container;
        return $this;
    }

    public function getContainers() : array
    {
        return $this->containers;
    }

    /**
     * @throws AttributeSetException
     */
    public function getContainer(string $name) : AttributeContainer
    {
        if(!$this->hasContainer($name))
            AttributeSetException::undefinedAttribute($name);
        return $this->containers[$name];
    }

    public function hasContainer(string $name) : bool
    {
        return array_key_exists($name, $this->containers);
    }

    public function reset() : self
    {
        $this->containers = [];
        return $this;
    }

    public function getRecord() : AttributeSetModel
    {
        return $this->makeAttributeSetModel()->firstOrFail($this->getKey());
    }

    public function getRecordAttributes(): Collection
    {
        return $this->getRecord()->attributes()->get();
    }

    public function fetch() : self
    {
        /** @var AttributeModel $attribute */
        foreach ($this->getRecordAttributes() as $attribute) {
            $container = $this->makeAttributeContainer();
            $container->setAttributeSet($this);
            $container->initialize($attribute);
            // TODO shift ->makeHidden('pivot') to AttributeContainer
            $this->push($container);
        }
        return $this;
    }
}