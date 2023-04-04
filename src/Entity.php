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

    public function __construct()
    {
        $this->attributeSet = $this->makeAttributeSet();
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
        $this->attributeSet = $attributeSet;
        return $this;
    }

    public function create(array $data) : Result
    {
        $result = new Result();
        $attrSet = $this->getAttributeSet();
        $attrSet->fetchContainers();
        foreach($data as $name => $value) {
            $container = $attrSet->getContainer($name);
            if(is_null($container)) continue;
            $action = $container->getEntityAction();
            $action->saveValue($value);
        }
        return $result->created();
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

    private function beforeSave(): int
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

    public function save() {
        $this->beforeSave();
    }

    public function delete() : Result
    {
        $result = new Result();
        return $result;
    }

    public function validate() : Result
    {
        $result = new Result();
        return $result;
    }

    public function toArray() : array
    {
        return [];
    }

    public function setField(string $name, mixed $value) : self
    {
        return $this;
    }

    public function getField(string $name) : mixed
    {
        return null;
    }
}