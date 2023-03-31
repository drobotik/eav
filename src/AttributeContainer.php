<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Model\AttributeModel;

class AttributeContainer
{
    protected Attribute $attribute;
    protected ValueManager $valueManager;
    protected Strategy $strategy;
    protected AttributeSet $attributeSet;

    public function make(string $className) {
        $supported = [
            AttributeSet::class,
            Attribute::class,
            ValueManager::class,
            Strategy::class
        ];

        if(!in_array($className, $supported)) {
            return false;
        }

        $instance = new $className();
        $instance->setAttributeContainer($this);
        return $instance;
    }

    public function makeAttributeSet() : self
    {
        $this->setAttributeSet($this->make(AttributeSet::class));
        return $this;
    }

    public function makeAttribute() : self
    {
        $this->setAttribute($this->make(Attribute::class));
        return $this;
    }

    public function makeStrategy() : self
    {
        $this->setStrategy($this->make(Strategy::class));
        return $this;
    }

    public function makeValueManager() : self
    {
        $this->setValueManager($this->make(ValueManager::class));
        return $this;
    }

    public function setAttribute(Attribute $attribute) : self
    {
        $attribute->setAttributeContainer($this);
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setValueManager(ValueManager $valueManager) : self
    {
        $valueManager->setAttributeContainer($this);
        $this->valueManager = $valueManager;
        return $this;
    }

    public function getValueManager(): ValueManager
    {
        return $this->valueManager;
    }

    public function setStrategy(Strategy $strategy) : self
    {
        $strategy->setAttributeContainer($this);
        $this->strategy = $strategy;
        return $this;
    }

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }

    public function setAttributeSet(AttributeSet $attrSet) : self
    {
        $attrSet->setAttributeContainer($this);
        $this->attributeSet = $attrSet;
        return $this;
    }

    public function getAttributeSet(): AttributeSet
    {
        return $this->attributeSet;
    }

    public function initialize(AttributeModel $attributeModel) : self
    {
        $attribute = new Attribute();
        $attribute->getBag()->setFields($attributeModel->toArray());
        $this->setAttribute($attribute);
        $className = $attribute->getStrategy();
        $strategy = $this->setStrategy(new $className)->getStrategy();
        $this->makeValueManager();
        $strategy->findAction();
        return $this;
    }
}