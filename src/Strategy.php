<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Illuminate\Validation\Validator;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\EavContainerTrait;

class Strategy implements StrategyInterface
{
    use EavContainerTrait;
    public bool $create = true;
    public bool $update = true;
    private Attribute    $attribute;
    private ValueManager $valueManager;

    public function createValue() : Result
    {
        $result = new Result();

        $container = $this->getEavContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getEntity();
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
        $container = $this->getEavContainer();
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

    public function updateValue() : Result
    {
        $result = new Result();
        $container = $this->getEavContainer();
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
        return $this->getEavContainer()
            ->getAttribute()
            ->getType()
            ->validationRule();
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
        $container = $this->getEavContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getEntity();
        $valueManager = $container->getValueManager();
        return [
            _VALUE::ENTITY_ID->column() => $entity->getKey(),
            _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
            _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
            _VALUE::VALUE->column() => $valueManager->getRuntime()
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
        $container = $this->getEavContainer();
        $attribute = $container->getAttribute();
        $valueManager = $container->getValueManager();
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
        $entity = $this->getEavContainer()->getEntity();
        return $entity->getKey()
            ? $this->updateAction()
            : $this->createAction();
    }

    public function deleteValue(): Result
    {
        $result = new Result();
        $container = $this->getEavContainer();
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