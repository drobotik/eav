<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Trait\EavContainerTrait;

class Entity
{
    use EavContainerTrait;

    private ?int $key = null;
    private int $domainKey;

    private AttributeSet $attributeSet;

    public function getKey(): ?int
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
}