<?php

namespace Kuperwood\Eav\Value;

use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;

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
        $entityKey = $entity->getKey();
        $domainKey = $entity->getDomainKey();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if(is_null($attributeKey) || is_null($entityKey) || is_null($domainKey)) {
            return $result->empty();
        }

        $record = $model
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributeKey)
            ->first();

        if(is_null($record)) {
            return $result->notFound();
        }

        $valueManager
            ->setKey($record->getKey())
            ->setStored($record->getValue())
            ->clearRuntime();

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
        $key = $valueManager->getKey();

        if(is_null($key)) {
            return $result->empty();
        }

        $record = $model->findOrFail($key);

        $deleted = $record->delete();
        if(!$deleted) {
            return $result->notDeleted();
        }

        $valueManager->clearStored()
            ->clearRuntime()
            ->setKey(null);

        return $result->deleted();
    }
}