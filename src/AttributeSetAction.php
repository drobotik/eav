<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Trait\ContainerTrait;
use Kuperwood\Eav\Value\ValueManager;

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

    public function initializeValueManager() : ValueManager
    {
        $container = $this->getAttributeContainer();
        $container->makeValueManager();
        $container->makeValueAction();
        $valueManager = $container->getValueManager();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $bag = $entity->getBag();
        $name = $attribute->getName();
        if($bag->hasField($name)) {
            $valueManager->setRuntime($bag->getField($name));
        }
        $strategy = $container->getStrategy();
        $strategy->find();
        return $valueManager;
    }

    public function initialize(AttributeModel $attributeModel) : self
    {
        $attribute = $this->initializeAttribute($attributeModel);
        $this->initializeStrategy($attribute);
        $this->initializeValueManager();
        return $this;
    }
}