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

use Drobotik\Eav\Traits\ContainerTrait;
use Drobotik\Eav\Value\ValueManager;

class AttributeSetAction
{
    use ContainerTrait;

    public function initializeAttribute(array $record): Attribute
    {
        $container = $this->getAttributeContainer();
        $attribute = new Attribute();
        $attribute->getBag()->setFields($record);
        $container->setAttribute($attribute);

        return $attribute;
    }

    public function initializeStrategy(Attribute $attribute): Strategy
    {
        $container = $this->getAttributeContainer();
        $className = $attribute->getStrategy();
        $strategy = new $className();
        $container->setStrategy($strategy);

        return $strategy;
    }

    public function initializeValueManager(): ValueManager
    {
        $container = $this->getAttributeContainer();
        $container->makeValueManager();
        $container->makeValueAction();
        $container->makeValueValidator();
        $valueManager = $container->getValueManager();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $bag = $entity->getBag();
        $name = $attribute->getName();
        if ($bag->hasField($name)) {
            $valueManager->setRuntime($bag->getField($name));
        }
        $strategy = $container->getStrategy();
        $strategy->find();

        return $valueManager;
    }

    public function initialize(array $attributeRecord): self
    {
        $attribute = $this->initializeAttribute($attributeRecord);
        $this->initializeStrategy($attribute);
        $this->initializeValueManager();

        return $this;
    }
}
