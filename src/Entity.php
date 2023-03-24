<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

class Entity
{
    private int $key;
    private int $domainKey;

    private AttributeSet $attributeSet;

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key): self
    {
        $this->key = $key;
        return $this;
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

    public function getAttributeSet(): AttributeSet
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSet $attributeSet): self
    {
        $this->attributeSet = $attributeSet;
        return $this;
    }
}