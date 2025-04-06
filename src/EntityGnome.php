<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav;

use Doctrine\DBAL\Exception;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Traits\SingletonsTrait;

class EntityGnome
{
    use SingletonsTrait;

    private Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @throws EntityException
     * @throws Exception
     */
    public function beforeSave(): int
    {
        $entity = $this->getEntity();
        $set = $entity->getAttributeSet();
        if (!$entity->hasKey()) {
            if (!$entity->hasDomainKey()) {
                EntityException::undefinedDomainKey();
            }
            if (!$set->hasKey()) {
                EntityException::undefinedAttributeSetKey();
            }
            $domainKey = $entity->getDomainKey();
            $this->checkDomainExists($domainKey);
            $setKey = $set->getKey();
            $this->checkAttrSetExist($setKey);
            $model = $this->makeEntityModel();
            $key = $model->create([
                _ENTITY::DOMAIN_ID => $domainKey,
                _ENTITY::ATTR_SET_ID => $setKey
            ]);
            $entity->setKey($key);

            return 1;
        }
        $key = $entity->getKey();
        $record = $this->checkEntityExists($key);
        $domainKey = (int) $record[_ENTITY::DOMAIN_ID];
        $setKey = (int) $record[_ENTITY::ATTR_SET_ID];

        $this->checkDomainExists($domainKey);
        $this->checkAttrSetExist($setKey);
        $set->setKey($setKey);
        $entity->setDomainKey($domainKey);

        return 2;
    }

    public function find(): Result
    {
        $result = new Result();

        $entity = $this->getEntity();

        if (!$entity->hasKey()) {
            return $result->empty();
        }
        $key = $entity->getKey();

        $model = $this->makeEntityModel();
        $record = $model->findByKey($key);

        if ($record === false) {
            return $result->notFound();
        }

        $entity->setDomainKey($record[_ENTITY::DOMAIN_ID]);
        $set = $entity->getAttributeSet();
        $set->setKey($record[_ENTITY::ATTR_SET_ID]);
        $set->fetchContainers();

        $result->found();

        return $result;
    }

    /**
     * @throws EntityException
     * @throws Exception
     */
    public function save(): Result
    {
        $entity = $this->getEntity();
        $result = new Result();
        $operationType = $this->beforeSave();
        $set = $entity->getAttributeSet();
        $set->fetchContainers();
        $valueResults = [];
        foreach ($set->getContainers() as $container) {
            $attribute = $container->getAttribute();
            $valueResults[$attribute->getName()] = $container->getStrategy()->save();
        }
        1 == $operationType
            ? $result->created()
            : $result->updated();
        $result->setData($valueResults);
        $bag = $entity->getBag();
        $bag->clear();

        return $result;
    }

    /**
     * @throws EntityException
     */
    public function delete()
    {
        $entity = $this->getEntity();
        $set = $entity->getAttributeSet();

        if (!$entity->hasKey()) {
            EntityException::undefinedEntityKey();
        }
        if (!$set->hasKey()) {
            EntityException::undefinedAttributeSetKey();
        }

        $result = new Result();
        $set->fetchContainers();
        $deleteResults = [];
        foreach ($set->getContainers() as $container) {
            $attribute = $container->getAttribute();
            $deleteResults[$attribute->getName()] = $container->getStrategy()->delete();
        }
        $entityModel = $this->makeEntityModel();
        $recordResult = $entityModel->deleteByKey($entity->getKey());
        if (!$recordResult) {
            return $result->notDeleted();
        }

        $entity->setKey(0);
        $entity->setDomainKey(0);
        $set->setKey(0);
        $set->resetContainers();

        $result->deleted();
        $result->setData($deleteResults);

        return $result;
    }

    public function validate(): Result
    {
        $result = new Result();
        $result->validationPassed();
        $entity = $this->getEntity();
        $set = $entity->getAttributeSet();
        $set->fetchContainers();
        $errors = [];
        foreach ($set->getContainers() as $container) {
            $validationResult = $container->getValueValidator()->validateField();
            if (!is_null($validationResult)) {
                $errors[$container->getAttribute()->getName()] = $validationResult;
            }
        }
        if (count($errors) > 0) {
            $result->validationFails();
            $result->setData($errors);
        }

        return $result;
    }

    public function toArray(): array
    {
        $result = [];
        $entity = $this->getEntity();
        foreach ($entity->getAttributeSet()->getContainers() as $container) {
            $result[$container->getAttribute()->getName()] = $container->getValueManager()->getValue();
        }

        return $result;
    }

    /**
     * @throws EntityException
     */
    private function checkEntityExists(int $key): array
    {
        $entity = $this->makeEntityModel();

        $result = $entity->findByKey($key);
        if ($result === false)
            EntityException::entityNotFound();

        return $result;
    }

    /**
     * @throws EntityException
     */
    private function checkDomainExists(int $key): void
    {
        $domain = $this->makeDomainModel();

        if ($domain->findByKey($key) === false)
            EntityException::domainNotFound();
    }

    /**
     * @throws EntityException
     */
    private function checkAttrSetExist(int $key): void
    {
        $model = $this->makeAttributeSetModel();

        if($model->findByKey($key) === false) {
            EntityException::attrSetNotFound();
        }
    }
}
