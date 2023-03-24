<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

class AttributeSet
{
    private int $key;
    private string $name;
    private array $attributes;
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

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity) : self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttributes() : array
    {
        return $this->attributes;
    }

}