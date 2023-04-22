<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Result\Result;

class Entity
{
    private int $key;
    private int $domainKey;
    private AttributeSet $attributeSet;
    private EntityBag $bag;
    private EntityGnome $manager;

    public function __construct()
    {
        $this->bag = (new EntityBag())->setEntity($this);
        $this->attributeSet = new AttributeSet();
        $this->attributeSet->setEntity($this);
        $this->manager = new EntityGnome($this);
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function hasKey(): bool
    {
        return isset($this->key) && 0 !== $this->key;
    }

    public function setDomainKey(int $key): self
    {
        $this->domainKey = $key;

        return $this;
    }

    public function getDomainKey(): int
    {
        return $this->domainKey;
    }

    public function hasDomainKey(): bool
    {
        return isset($this->domainKey) && 0 !== $this->domainKey;
    }

    public function getGnome(): EntityGnome
    {
        return $this->manager;
    }

    public function getAttributeSet(): ?AttributeSet
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSet $attributeSet): self
    {
        $attributeSet->setEntity($this);
        $this->attributeSet = $attributeSet;

        return $this;
    }

    public function getBag(): EntityBag
    {
        return $this->bag;
    }

    public function find(): Result
    {
        return $this->getGnome()->find();
    }

    public function save(): Result
    {
        return $this->getGnome()->save();
    }

    public function delete(): Result
    {
        return $this->getGnome()->delete();
    }

    public function validate(): Result
    {
        return $this->getGnome()->validate();
    }

    public function toArray(): array
    {
        return $this->getGnome()->toArray();
    }
}
