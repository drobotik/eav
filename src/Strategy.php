<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_RESULT;
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

    public function createAction() : Result
    {
        $container = $this->getAttributeContainer();
        $valueAction = $container->getValueAction();

        if (!$this->isCreate()) {
            $container->getValueManager()->clearRuntime();
            return (new Result())->notAllowed();
        }

        $this->beforeCreate();
        $result = $valueAction->create();
        $this->afterCreate();
        return $result;
    }

    public function updateAction() : Result
    {
        $container = $this->getAttributeContainer();
        $valueManager = $container->getValueManager();
        $valueAction = $container->getValueAction();

        if (!$this->isUpdate()) {
            $valueManager->clearRuntime();
            return (new Result())->notAllowed();
        }

        $this->beforeUpdate();
        $result = $valueManager->getKey()
            ? $valueAction->update()
            : $valueAction->create();
        $this->afterUpdate();
        return $result;
    }

    public function deleteAction() : Result
    {
        $container = $this->getAttributeContainer();
        $valueAction = $container->getValueAction();

        $this->beforeDelete();
        $result = $valueAction->delete();
        $this->afterDelete();
        return $result;
    }

    public function findAction(): Result
    {
        return $this->getAttributeContainer()->getValueAction()->find();
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