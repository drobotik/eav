<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

class Attribute
{
    private AttributeBag $bag;

    public function getBag(): AttributeBag
    {
        return $this->bag;
    }

    public function setBag(AttributeBag $bag): self
    {
        $this->bag = $bag;
        return $this;
    }
}