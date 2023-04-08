<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Interface\EavStrategyInterface;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;

class Strategy implements StrategyInterface, EavStrategyInterface
{
    use ContainerTrait;
    public bool $create = true;
    public bool $update = true;

    public function create() : Result
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

    public function update() : Result
    {
        $container = $this->getAttributeContainer();
        $valueManager = $container->getValueManager();
        $valueAction = $container->getValueAction();

        if (!$this->isUpdate()) {
            $valueManager->clearRuntime();
            return (new Result())->notAllowed();
        }

        $this->beforeUpdate();
        $result = $valueManager->hasKey()
            ? $valueAction->update()
            : $valueAction->create();
        $this->afterUpdate();
        return $result;
    }

    public function delete() : Result
    {
        $container = $this->getAttributeContainer();
        $valueAction = $container->getValueAction();

        $this->beforeDelete();
        $result = $valueAction->delete();
        $this->afterDelete();
        return $result;
    }

    public function find(): Result
    {
        return $this->getAttributeContainer()->getValueAction()->find();
    }

    public function validate(): Result
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

    public function save(): Result
    {
        $entity = $this->getAttributeContainer()->getAttributeSet()->getEntity();
        return $entity->getKey()
            ? $this->update()
            : $this->create();
    }

    public function afterCreate() : void {}

    public function beforeCreate() : void {}

    public function beforeUpdate() : void {}

    public function afterUpdate() : void {}

    public function beforeDelete() : void {}

    public function afterDelete() : void {}

    public function rules(): ?array
    {
        return null;
    }

    public function isCreate() : bool
    {
        return $this->create;
    }
    public function isUpdate() : bool
    {
        return $this->update;
    }
}