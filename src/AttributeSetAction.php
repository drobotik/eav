<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Trait\ContainerTrait;

class AttributeSetAction
{
    use ContainerTrait;

    public function initializeAttribute(AttributeModel $record) : Attribute
    {
        $container = $this->getAttributeContainer();
        $attribute = new Attribute();
        $data = $record->makeHidden('pivot')->toArray();
        $attribute->getBag()->setFields($data);
        $container->setAttribute($attribute);
        return $attribute;
    }

    public function initializeStrategy(Attribute $attribute) : Strategy
    {
        $container = $this->getAttributeContainer();
        $className = $attribute->getStrategy();
        $strategy = new $className;
        $container->setStrategy($strategy);
        return $strategy;
    }

    public function initializeRuntimeValue() : void
    {
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $bag = $entity->getBag();
        $name = $attribute->getName();
        if($bag->hasField($name)) {
            $valueManager = $container->getValueManager();
            $valueManager->setRuntime($bag->getField($name));
        }
    }

    public function initialize(AttributeModel $attributeModel) : self
    {
        $container = $this->getAttributeContainer();
        $attribute = $this->initializeAttribute($attributeModel);
        $strategy = $this->initializeStrategy($attribute);
        $container->makeValueManager();
        $this->initializeRuntimeValue();
        $container->makeValueAction();
        $strategy->find();
        return $this;
    }
}