<?php

namespace Drobotik\Eav;

use Drobotik\Eav\Value\ValueAction;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;

class AttributeContainer
{
    private array $supported = [
        AttributeSet::class,
        AttributeSetAction::class,
        Attribute::class,
        Strategy::class,
        ValueManager::class,
        ValueValidator::class,
        ValueAction::class
    ];

    protected Attribute $attribute;
    protected Strategy $strategy;
    protected AttributeSet $attributeSet;
    protected AttributeSetAction $attributeSetAction;
    protected ValueManager $valueManager;
    protected ValueValidator $valueValidator;
    protected ValueAction $valueAction;

    public function make(string $className) {
        if(!in_array($className, $this->supported)) {
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

    public function makeAttributeSetAction() : self
    {
        $this->setAttributeSetAction($this->make(AttributeSetAction::class));
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

    public function makeValueValidator() : self
    {
        $this->setValueValidator($this->make(ValueValidator::class));
        return $this;
    }

    public function makeValueAction() : self
    {
        $this->setValueAction($this->make(ValueAction::class));
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

    public function setValueValidator(ValueValidator $valueValidator) : self
    {
        $valueValidator->setAttributeContainer($this);
        $this->valueValidator = $valueValidator;
        return $this;
    }

    public function getValueValidator(): ValueValidator
    {
        return $this->valueValidator;
    }

    public function setValueAction(ValueAction $valueAction) : self
    {
        $valueAction->setAttributeContainer($this);
        $this->valueAction = $valueAction;
        return $this;
    }

    public function getValueAction(): ValueAction
    {
        return $this->valueAction;
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

    public function setAttributeSetAction(AttributeSetAction $attrSetAction) : self
    {
        $attrSetAction->setAttributeContainer($this);
        $this->attributeSetAction = $attrSetAction;
        return $this;
    }

    public function getAttributeSetAction(): AttributeSetAction
    {
        return $this->attributeSetAction;
    }
}