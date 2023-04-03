<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;

class Entity
{
    use ContainerTrait;

    private ?int $key = null;
    private ?int $domainKey = null;
    private AttributeSet $attributeSet;

    public function getKey(): ?int
    {
        return $this->key;
    }

    public function setKey(?int $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setDomainKey(?int $key): self
    {
        $this->domainKey = $key;
        return $this;
    }

    public function getDomainKey(): ?int
    {
        return $this->domainKey;
    }

    public function getAttributeSet(): AttributeSet
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSet $attributeSet): self
    {
        $this->attributeSet = $attributeSet;
        return $this;
    }

    public function create(array $data) : Result
    {
        $result = new Result();
        $attrSet = $this->getAttributeSet();
        $attrSet->fetchContainers();
        foreach($data as $name => $value) {
            $container = $attrSet->getContainer($name);
            if(is_null($container)) continue;
            $action = $container->getEntityAction();
            $action->saveValue($value);
        }
        return $result->created();
    }

    public function find() : Result
    {
        $result = new Result();
        return $result;
    }

    public function update() : Result
    {
        $result = new Result();
        return $result;
    }

    public function delete() : Result
    {
        $result = new Result();
        return $result;
    }

    public function validate() : Result
    {
        $result = new Result();
        return $result;
    }

    public function toArray() : array
    {
        return [];
    }

    public function setField(string $name, mixed $value) : self
    {
        return $this;
    }

    public function getField(string $name) : mixed
    {
        return null;
    }
}