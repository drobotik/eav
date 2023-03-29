<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Illuminate\Validation\Validator;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Result\Result;

class Strategy implements StrategyInterface
{
    public bool $create = true;
    public bool $update = true;
    private Attribute    $attribute;
    private ValueManager $valueManager;

    public function getAttribute() : Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute) : self
    {
        $this->attribute = $attribute;
        return $this;
    }
    
    public function getValueManager() : ValueManager
    {
        return $this->valueManager;
    }

    public function setValueManager(ValueManager $value) : self
    {
        $this->valueManager = $value;
        return $this;
    }

    public function createValue() : Result
    {
        $result = new Result();

        $attribute = $this->getAttribute();
        $set = $attribute->getAttributeSet();
        $entity = $set->getEntity();
        $valueManager = $this->getValueManager();
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
            ->setVal($valueManager->getRuntime())
            ->save();

        $model->refresh();
        $valueManager->setStored($model->getVal())
            ->setKey($model->getKey())
            ->clearRuntime();

        return $result->created();
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
        $valueManager = $this->getValueManager();
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

    public function updateValue() : Result
    {
        $result = new Result();
        $attribute = $this->getAttribute();
        $valueManager = $this->getValueManager();
        $model = $attribute->getValueModel();

        if (!$this->isUpdate()) {
            $valueManager->clearRuntime();
            return $result->notAllowed();
        }

        if(!$valueManager->isRuntime()) {
            return $result->empty();
        }

        $record = $model->findOrFail($valueManager->getKey());
        $record->setVal($valueManager->getRuntime())
            ->save();
        $record->refresh();
        $valueManager->setStored($record->getVal())
            ->clearRuntime();

        return $result->updated();
    }


    public function rules(): ?array
    {
        return null;
    }

    public function getDefaultValueRule() {
        return $this->getAttribute()->getType()->validationRule();
    }

    public function getRules() {
        $rules = $this->rules();
        return [
            _VALUE::ENTITY_ID->column() => ['required', 'integer'],
            _VALUE::DOMAIN_ID->column() => ['required','integer'],
            _VALUE::ATTRIBUTE_ID->column() => ['required','integer'],
            _VALUE::VALUE->column() => is_null($rules)
                ? $this->getDefaultValueRule()
                : $rules
        ];
    }

    public function getValidatedData() : array
    {
        $attribute = $this->getAttribute();
        $attrSet = $attribute->getAttributeSet();
        $entity = $attrSet->getEntity();
        return [
            _VALUE::ENTITY_ID->column() => $entity->getKey(),
            _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
            _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
            _VALUE::VALUE->column() => $this->getValueManager()->getRuntime()
        ];
    }

    public function getValidator() : Validator
    {
        return Container::getInstance()->getValidator()->make(
            $this->getValidatedData(),
            $this->getRules()
        );
    }

    public function validateAction(): Result
    {
        $result = new Result();
        return $result;
    }

    public function findAction(): Result
    {
        $result = new Result();
        $attribute = $this->getAttribute();
        $valueManager = $this->getValueManager();
        $model = $attribute->getValueModel();
        $key = $valueManager->getKey();


        if(is_null($key)) {
            return $result->empty();
        }

        $record = $model->whereKey($key)->first();

        if(is_null($record)) {
            return $result->notFound();
        }

        $valueManager->setStored($record->getVal())
            ->clearRuntime();

        return $result->found();
    }

    public function saveAction(): Result
    {
        $entity = $this->getAttribute()->getAttributeSet()->getEntity();
        return $entity->getKey()
            ? $this->updateAction()
            : $this->createAction();
    }

    public function deleteValue(): Result
    {
        $result = new Result();
        $attribute = $this->getAttribute();
        $valueManager = $this->getValueManager();
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
}