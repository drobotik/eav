<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Value;

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\ContainerTrait;

class ValueAction
{
    use ContainerTrait;

    public function create() : Result
    {
        $result = new Result();

        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if(!$valueManager->isRuntime()) {
            return $result->empty();
        }

        $model->setDomainKey($entity->getDomainKey())
            ->setEntityKey($entity->getKey())
            ->setAttrKey($attribute->getKey())
            ->setValue($valueManager->getRuntime())
            ->save();

        $model->refresh();
        $valueManager->setStored($model->getValue())
            ->setKey($model->getKey())
            ->clearRuntime();

        return $result->created();
    }

    public function find(): Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $attributeKey = $attribute->getKey();
        $entity = $container->getAttributeSet()->getEntity();

        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if(is_null($attributeKey) || !$entity->hasKey()) {
            return $result->empty();
        }

        $entityKey = $entity->getKey();

        $record = $model
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributeKey)
            ->first();

        if(is_null($record)) {
            return $result->notFound();
        }

        $valueManager
            ->setKey($record->getKey())
            ->setStored($record->getValue());

        return $result->found();
    }

    public function update() : Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if(!$valueManager->isRuntime()) {
            return $result->empty();
        }

        $record = $model->findOrFail($valueManager->getKey());
        $record->setValue($valueManager->getRuntime())
            ->save();
        $record->refresh();
        $valueManager->setStored($record->getValue())
            ->clearRuntime();

        return $result->updated();
    }


    public function delete(): Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if(!$valueManager->hasKey()) {
            return $result->empty();
        }

        $record = $model->findOrFail($valueManager->getKey());

        $deleted = $record->delete();
        if(!$deleted) {
            return $result->notDeleted();
        }

        $valueManager->clearStored()
            ->clearRuntime()
            ->setKey(0);

        return $result->deleted();
    }
}