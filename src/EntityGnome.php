<?php

declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\SingletonsTrait;
use Throwable;

class EntityGnome
{
    use SingletonsTrait;

    private Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity() : Entity {
        return $this->entity;
    }

    private function checkEntityExists(int $key) : EntityModel
    {
        try {
            return $this->makeEntityModel()->findOrFail($key);
        } catch(Throwable $e) {
            EntityException::entityNotFound();
        }
    }

    private function checkDomainExists(int $key) : DomainModel
    {
        try {
            return $this->makeDomainModel()->findOrFail($key);
        } catch(Throwable $e) {
            EntityException::domainNotFound();
        }
    }

    private function checkAttrSetExist(int $key) : AttributeSetModel
    {
        try {
            return $this->makeAttributeSetModel()->findOrFail($key);
        } catch(Throwable $e) {
            EntityException::attrSetNotFound();
        }
    }

    public function beforeSave(): int
    {
        $entity = $this->getEntity();
        $set = $entity->getAttributeSet();
        if (!$entity->hasKey()) {
            if(!$entity->hasDomainKey()) {
                EntityException::undefinedDomainKey();
            }
            if(!$set->hasKey()) {
                EntityException::undefinedAttributeSetKey();
            }
            $domainKey = $entity->getDomainKey();
            $this->checkDomainExists($domainKey);
            $setKey = $set->getKey();
            $this->checkAttrSetExist($setKey);
            $model = $this->makeEntityModel();
            $model->setDomainKey($domainKey);
            $model->setAttrSetKey($setKey);
            $model->save();
            $entity->setKey($model->getKey());
            return 1;
        } else {
            $key = $entity->getKey();
            $record = $this->checkEntityExists($key);
            $this->checkDomainExists($record->getDomainKey());
            $this->checkAttrSetExist($record->getAttrSetKey());
            $set->setKey($record->getAttrSetKey());
            $entity->setDomainKey($record->getDomainKey());
            return 2;
        }
    }

    public function find() : Result
    {
        $result = new Result();

        $entity = $this->getEntity();

        if(!$entity->hasKey()) {
            return $result->empty();
        }
        $key = $entity->getKey();

        $model = $this->makeEntityModel();
        $record = $model->find($key);

        if(is_null($record)) {
            return $result->notFound();
        }

        $entity->setDomainKey($record->getDomainKey());
        $set = $this->makeAttributeSet();
        $set->setKey($record->getAttrSetKey());
        $set->fetchContainers();
        $set->setEntity($entity);
        $entity->setAttributeSet($set);

        $result->found();
        return $result;
    }

    public function save() : Result
    {
        $entity = $this->getEntity();
        $result = new Result();
        $operationType = $this->beforeSave();
        $set = $entity->getAttributeSet();
        $set->fetchContainers();
        $valueResults = [];
        foreach($set->getContainers() as $container) {
            $attribute = $container->getAttribute();
            $valueResults[$attribute->getName()] = $container->getStrategy()->save();
        }
        $operationType == 1
            ? $result->created()
            : $result->updated();
        $result->setData($valueResults);
        $bag = $entity->getBag();
        $bag->clear();
        return $result;
    }

    public function delete() {
        $result = new Result();

        $entity = $this->getEntity();

        $set = $entity->getAttributeSet();
        $set->fetchContainers();
        $deleteResults = [];
        foreach ($set->getContainers() as $container) {
            $attribute = $container->getAttribute();
            $deleteResults[$attribute->getName()] = $container->getStrategy()->delete();
        }
        $recordResult = $this->makeEntityModel()->findAndDelete($entity->getKey());
        if(!$recordResult) {
            return $result->notDeleted();
        }
        $result->deleted();
        $result->setData($deleteResults);
        return $result;
    }

    public function validate() : Result
    {
        $result = new Result();
        $result->validationPassed();
        $entity = $this->getEntity();
        $set = $entity->getAttributeSet();
        $set->fetchContainers();
        $errors = [];
        foreach ($set->getContainers() as $container) {
            $validationResult = $container->getValueValidator()->validateField();
            if(!is_null($validationResult))
                $errors[$container->getAttribute()->getName()] = $validationResult;
        }
        if(count($errors) > 0) {
            $result->validationFails();
            $result->setData($errors);
        }
        return $result;
    }

    public function toArray() : array
    {
        $result = [];
        $entity = $this->getEntity();
        foreach ($entity->getAttributeSet()->getContainers() as $container) {
            $result[$container->getAttribute()->getName()] = $container->getValueManager()->getValue();
        }
        return $result;
    }
}