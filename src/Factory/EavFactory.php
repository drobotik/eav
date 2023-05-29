<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Factory;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Result\Result;
use Faker\Generator;

class EavFactory
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function createDomain(array $data = []): DomainModel
    {
        $defaultData = [
            _DOMAIN::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new DomainModel();
        $model->setName($input[_DOMAIN::NAME->column()]);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createEntity(?int $domainKey = null, ?int $setKey = null): EntityModel
    {
        if (is_null($domainKey))
        {
            $domain = $this->createDomain();
            $domainKey = $domain->getKey();
        }
        if (is_null($setKey))
        {
            $attrSet = $this->createAttributeSet($domainKey);
            $setKey = $attrSet->getKey();
        }
        $model = new EntityModel();
        $model->setDomainKey($domainKey);
        $model->setAttrSetKey($setKey);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createAttributeSet(?int $domainKey = null, array $data = []): AttributeSetModel
    {
        if (is_null($domainKey)) {
            $domain = $this->createDomain();
            $domainKey = $domain->getKey();
        }
        $defaultData = [
            _SET::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeSetModel();
        $model->setDomainKey($domainKey)
            ->setName($input[_SET::NAME->column()]);
        
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createGroup(?int $setKey = null, array $data = []): AttributeGroupModel
    {
        if (is_null($setKey)) {
            $set = $this->createAttributeSet();
            $setKey = $set->getKey();
        }
        $defaultData = [
            _GROUP::SET_ID->column() => $setKey,
            _GROUP::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeGroupModel();
        $model->setAttrSetKey($setKey)
            ->setName($input[_SET::NAME->column()]);
        
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createAttribute(?int $domainKey = null, array $data = []): AttributeModel
    {
        if (is_null($domainKey)) {
            $domain = $this->createDomain();
            $domainKey = $domain->getKey();
        }
        $defaultData = [
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => $this->faker->slug(2),
            _ATTR::TYPE->column() => _ATTR::TYPE->default(),
            _ATTR::STRATEGY->column() => _ATTR::STRATEGY->default(),
            _ATTR::SOURCE->column() => _ATTR::SOURCE->default(),
            _ATTR::DEFAULT_VALUE->column() => _ATTR::DEFAULT_VALUE->default(),
            _ATTR::DESCRIPTION->column() => _ATTR::DESCRIPTION->default(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeModel();
        $model->setName($input[_ATTR::NAME->column()])
            ->setDomainKey($domainKey)
            ->setType($input[_ATTR::TYPE->column()])
            ->setStrategy($input[_ATTR::STRATEGY->column()])
            ->setSource($input[_ATTR::SOURCE->column()])
            ->setDefaultValue($input[_ATTR::DEFAULT_VALUE->column()])
            ->setDescription($input[_ATTR::DESCRIPTION->column()]);
        
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createPivot(int $domainKey, int $setKey, int $groupKey, int $attributeKey): PivotModel
    {
        $model = new PivotModel();
        $model->setDomainKey($domainKey)
            ->setAttrSetKey($setKey)
            ->setGroupKey($groupKey)
            ->setAttrKey($attributeKey);
        
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createValue(ATTR_TYPE $type, int $domainKey, int $entityKey, int $attributeKey, $value): ValueBase
    {
        $model = $type->model();
        $model->setDomainKey($domainKey)
            ->setEntityKey($entityKey)
            ->setAttrKey($attributeKey)
            ->setValue($value);
        
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createEavEntity(array $config, int $domainKey, int $setKey): Result
    {
        $result = new Result();
        $result->created();
        $factory = new EntityFactory();
        $result->setData($factory->create($config, $domainKey, $setKey));

        return $result;
    }
}
