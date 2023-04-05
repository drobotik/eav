<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;
use Kuperwood\Eav\Trait\SingletonsTrait;
use Throwable;

class Entity
{
    use SingletonsTrait;
    use ContainerTrait;

    private int $key;
    private int $domainKey;
    private AttributeSet $attributeSet;
    private Transporter $bag;

    public function __construct()
    {
        $this->setAttributeSet($this->makeAttributeSet());
        $bag = new EntityBag();
        $bag->setEntity($this);
        $this->bag = $bag;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function hasKey(): bool
    {
        return isset($this->key);
    }

    public function setDomainKey(?int $key): self
    {
        $this->domainKey = $key;
        return $this;
    }

    public function getDomainKey(): ?int
    {
        return $this->domainKey;
    }

    public function hasDomainKey(): bool
    {
        return isset($this->domainKey);
    }

    public function getAttributeSet(): ?AttributeSet
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSet $attributeSet): self
    {
        $attributeSet->setEntity($this);
        $this->attributeSet = $attributeSet;
        return $this;
    }

    public function getBag() : EntityBag
    {
        return $this->bag;
    }
    public function find() : Result
    {
        $result = new Result();

        if(!$this->hasKey()) {
            return $result->empty();
        }
        $key = $this->getKey();

        $model = $this->makeEntityModel();
        $record = $model->find($key);

        if(is_null($record)) {
            return $result->notFound();
        }

        $this->setDomainKey($record->getDomainKey());
        $set = $this->makeAttributeSet();
        $set->setKey($record->getAttrSetKey());
        $set->fetchContainers();
        $set->setEntity($this);
        $this->setAttributeSet($set);

        $result->found();
        return $result;
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
        $set = $this->getAttributeSet();
        if (!$this->hasKey()) {
            if(!$this->hasDomainKey()) {
                EntityException::undefinedDomainKey();
            }
            if(!$set->hasKey()) {
                EntityException::undefinedAttributeSetKey();
            }
            $domainKey = $this->getDomainKey();
            $this->checkDomainExists($domainKey);
            $setKey = $set->getKey();
            $this->checkAttrSetExist($setKey);
            $model = $this->makeEntityModel();
            $model->setDomainKey($domainKey);
            $model->setAttrSetKey($setKey);
            $model->save();
            $this->setKey($model->getKey());
            return 1;
        } else {
            $key = $this->getKey();
            $record = $this->checkEntityExists($key);
            $this->checkDomainExists($record->getDomainKey());
            $this->checkAttrSetExist($record->getAttrSetKey());
            $set->setKey($record->getAttrSetKey());
            $this->setDomainKey($record->getDomainKey());
            return 2;
        }
    }

    public function save(): Result
    {
        $result = new Result();
        $operationType = $this->beforeSave();
        $set = $this->getAttributeSet();
        $set->fetchContainers();
        $bag = $this->getBag();
        $data = $bag->getData();
        $valueResults = [];
        foreach($data as $name => $value) {
            $container = $set->getContainer($name);
            if(is_null($container)) continue;
            $valueResults[$name] = $container->getEntityAction()->saveValue($value);
        }
        $operationType == 1
            ? $result->created()
            : $result->updated();
        $result->setData($valueResults);
        $bag->clear();
        return $result;
    }

    public function delete() : Result
    {
        $result = new Result();
        return $result;
    }

    public function validate() : Result
    {
        $result = new Result();
        $result->validationPassed();
        $set = $this->getAttributeSet();
        $set->fetchContainers();
        $errors = [];
        foreach ($set->getContainers() as $container) {
            $validationResult = $container->getEntityAction()->validateField();
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
        $set = $this->getAttributeSet();
        $result = [];
        foreach ($set->getContainers() as $container) {
            $result[$container->getAttribute()->getName()] = $container->getValueManager()->getValue();
        }
        return $result;
    }

}