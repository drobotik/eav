<?php

namespace Kuperwood\Eav\Value;

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