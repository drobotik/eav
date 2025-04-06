<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Factory;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_FACTORY;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Exception\EntityFactoryException;
use Kuperwood\Eav\Result\EntityFactoryResult;
use Kuperwood\Eav\Traits\SingletonsTrait;

class EntityFactory
{
    use SingletonsTrait;

    private EntityFactoryResult $result;

    private function makeNewResult() : EntityFactoryResult
    {
        $this->result = new EntityFactoryResult();
        return $this->result;
    }

    public function getResult() : EntityFactoryResult
    {
        return $this->result;
    }

    public function create(array $fields, int $domainKey, int $setKey): EntityFactoryResult
    {
        $result = $this->makeNewResult();
        $result->setDomainKey($domainKey);
        $result->setSetKey($setKey);
        $this->validateFields($fields);
        $entityKey = $this->makeEavFactory()->createEntity($domainKey, $setKey);
        $result->setEntityKey($entityKey);
        foreach ($fields as $field) {
            $this->handleField($field);
        }
        return $result;
    }

    private function validateFields(array $fields) : void
    {
        $result = $this->getResult();
        $groupModel = $this->makeGroupModel();
        $setKey = $result->getSetKey();
        foreach($fields as $field) {
            if (!key_exists(ATTR_FACTORY::GROUP, $field)) {
                throw new EntityFactoryException("Group key must be provided!");
            }
        }
        $groups = array_unique(array_column($fields, ATTR_FACTORY::GROUP));
        foreach($groups as $groupKey)
        {
            if(!$groupModel->checkGroupInAttributeSet($setKey, $groupKey))
            {
                throw new EntityFactoryException("This group is not belongs to attribute set");
            }
        }
    }

    public function handleAttribute($config) : int
    {
        $result = $this->getResult();
        $domainKey = $result->getDomainKey();
        $factory = $this->makeEavFactory();
        $attributeModel = $this->makeAttributeModel();
        $record = $attributeModel->findByName($config[_ATTR::NAME], $domainKey);
        if (!is_bool($record)) {
            $attrKey = $record[_ATTR::ID];
            $attrData = array_intersect_key($config, [
                _ATTR::NAME => null,
                _ATTR::TYPE => null,
                _ATTR::STRATEGY => null,
                _ATTR::SOURCE => null,
                _ATTR::DEFAULT_VALUE => null,
                _ATTR::DESCRIPTION => null
            ]);
            $attributeModel->updateByArray($attrKey, $attrData);
        } else {
            $attrKey = $factory->createAttribute($domainKey, $config);
        }
        $result->addAttribute([
            _ATTR::ID => $attrKey,
            _ATTR::NAME => $config[_ATTR::NAME]
        ]);

        return $attrKey;
    }

    public function handlePivot(int $attrKey, int $groupKey) : int
    {
        $result = $this->getResult();
        $domainKey = $result->getDomainKey();
        $setKey = $result->getSetKey();
        $pivotModel = $this->makePivotModel();
        $factory = $this->makeEavFactory();
        $pivotRecord = $pivotModel->findOne($domainKey, $setKey, $groupKey, $attrKey);
        if($pivotRecord === false)
            $pivotKey = $factory->createPivot($domainKey, $setKey, $groupKey, $attrKey);
        else {
            $pivotKey = $pivotRecord[_PIVOT::ID];
        }

        $result->addPivot($attrKey, $pivotKey);
        return $pivotKey;
    }

    /**
     * @throws \Exception
     */
    public function handleValue($type, int $entityKey, int $attrKey, $value)
    {
        $result = $this->getResult();
        $valueModel = $this->makeValueModel();
        $domainKey = $result->getDomainKey();

        $record = $valueModel->find($type, $domainKey, $entityKey, $attrKey);
        if($record === false) {
            $key = $valueModel->create($type, $domainKey, $entityKey, $attrKey, $value);
        } else {
            $key = $record[_VALUE::ID];
            $valueModel->update($type, $domainKey, $entityKey, $attrKey, $value);
        }
        $result->addValue($attrKey, $key);
        return $key;
    }

    private function handleField(array $field)
    {
        $result = $this->getResult();
        $entityKey = $result->getEntityKey();

        if (!key_exists(ATTR_FACTORY::ATTRIBUTE, $field)) {
            EntityFactoryException::undefinedAttributeArray();
        }
        $attrConfig = $field[ATTR_FACTORY::ATTRIBUTE];
        if (!key_exists(_ATTR::NAME, $attrConfig)) {
            AttributeException::undefinedAttributeName();
        }
        if (!key_exists(_ATTR::TYPE, $attrConfig)) {
            AttributeException::undefinedAttributeType();
        }

        $attrType = ATTR_TYPE::getCase($attrConfig[_ATTR::TYPE]);
        $attrKey = $this->handleAttribute($attrConfig);
        $this->handlePivot($attrKey, $field[ATTR_FACTORY::GROUP]);

        if(isset($field[ATTR_FACTORY::VALUE]))
            $this->handleValue($attrType, $entityKey, $attrKey, $field[ATTR_FACTORY::VALUE]);
    }
}
