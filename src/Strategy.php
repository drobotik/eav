<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;

class Strategy implements StrategyInterface
{
    use ContainerTrait;
    public bool $create = true;
    public bool $update = true;
    private Attribute    $attribute;
    private ValueManager $valueManager;

    public function createValue() : Result
    {
        $result = new Result();

        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if (!$this->isCreate()) {
            $valueManager->clearRuntime();
            return $result->notAllowed();
        }

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

    public function updateValue() : Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $valueManager = $container->getValueManager();
        $model = $attribute->getValueModel();

        if (!$this->isUpdate()) {
            $valueManager->clearRuntime();
            return $result->notAllowed();
        }

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


    public function deleteValue(): Result
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

        $this->beforeDelete();

        $deleted = $record->delete();
        if(!$deleted) {
            return $result->notDeleted();
        }

        $valueManager->clearStored()
            ->clearRuntime()
            ->setKey(null);

        $this->afterDelete();

        return $result->deleted();
    }

    public function createAction() : Result
    {
        $this->beforeCreate();
        $result = $this->createValue();
        $this->afterCreate();
        return $result;
    }

    public function updateAction() : Result
    {
        $container = $this->getAttributeContainer();
        $valueManager = $container->getValueManager();
        $this->beforeUpdate();

        $result = $valueManager->getKey()
            ? $this->updateValue()
            : $this->createValue();

        $this->afterUpdate();
        return $result;
    }

    public function deleteAction() : Result
    {
        $this->beforeDelete();
        $result = $this->deleteValue();
        $this->afterDelete();
        return $result;
    }

    public function findAction(): Result
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

    public function saveAction(): Result
    {
        $entity = $this->getAttributeContainer()->getAttributeSet()->getEntity();
        return $entity->getKey()
            ? $this->updateAction()
            : $this->createAction();
    }

    public function afterCreate() : void {}

    public function beforeCreate() : void {}

    public function beforeUpdate() : void {}

    public function afterUpdate() : void {}

    public function beforeDelete() : void {}

    public function afterDelete() : void {}

    public function isCreate() : bool
    {
        return $this->create;
    }
    public function isUpdate() : bool
    {
        return $this->update;
    }

    public function rules(): ?array
    {
        return null;
    }

    public function validateAction(): Result
    {
        $result = new Result();
        $validator = $this->getAttributeContainer()->getValueValidator()->getValidator();
        if($validator->fails()) {
            return $result->validationFails()
                ->setData($validator->errors());
        }
        return $result->setCode(_RESULT::VALIDATION_PASSED->code())
            ->setMessage(_RESULT::VALIDATION_PASSED->message());
    }
}