<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Interface\StrategyInterface;

class Attribute
{
    private AttributeBag $bag;
    private AttributeSet $attributeSet;
    private StrategyInterface $strategy;

    private Source $source;

    public function getBag(): AttributeBag
    {
        return $this->bag;
    }

    public function setBag(AttributeBag $bag): self
    {
        $this->bag = $bag;
        return $this;
    }

    public function getStrategy(): StrategyInterface
    {
        return $this->strategy;
    }

    public function setStrategy(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function getAttributeSet() : AttributeSet
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSet $attributeSet) : self
    {
        $this->attributeSet = $attributeSet;
        return $this;
    }

    public function getSource() : Source
    {
        return $this->source;
    }

    public function setSource(Source $source) : self
    {
        $this->source = $source;
        return $this;
    }
}