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

    public function createEntity(?DomainModel $domain = null, ?AttributeSetModel $attrSet = null): EntityModel
    {
        if (is_null($domain)) {
            $domain = $this->createDomain();
        }
        if (is_null($attrSet)) {
            $attrSet = $this->createAttributeSet($domain);
        }
        $model = new EntityModel();
        $model->setDomainKey($domain->getKey());
        $model->setAttrSetKey($attrSet->getKey());
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createAttributeSet(?DomainModel $domain = null, array $data = []): AttributeSetModel
    {
        if (is_null($domain)) {
            $domain = $this->createDomain();
        }
        $defaultData = [
            _SET::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeSetModel();
        $model->setDomainKey($domain->getKey())
            ->setName($input[_SET::NAME->column()]);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createGroup(?AttributeSetModel $set = null, array $data = []): AttributeGroupModel
    {
        if (is_null($set)) {
            $set = $this->createAttributeSet();
        }
        $defaultData = [
            _GROUP::SET_ID->column() => $set->getKey(),
            _GROUP::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeGroupModel();
        $model->setAttrSetKey($set->getKey())
            ->setName($input[_SET::NAME->column()]);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createAttribute(?DomainModel $domain = null, array $data = []): AttributeModel
    {
        if (is_null($domain)) {
            $domain = $this->createDomain();
        }
        $defaultData = [
            _ATTR::DOMAIN_ID->column() => $domain->getKey(),
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
            ->setDomainKey($domain->getKey())
            ->setType($input[_ATTR::TYPE->column()])
            ->setStrategy($input[_ATTR::STRATEGY->column()])
            ->setSource($input[_ATTR::SOURCE->column()])
            ->setDefaultValue($input[_ATTR::DEFAULT_VALUE->column()])
            ->setDescription($input[_ATTR::DESCRIPTION->column()]);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createPivot(DomainModel $domain, AttributeSetModel $set, AttributeGroupModel $group, AttributeModel $attribute): PivotModel
    {
        $model = new PivotModel();
        $model->setDomainKey($domain->getKey())
            ->setAttrSetKey($set->getKey())
            ->setGroupKey($group->getKey())
            ->setAttrKey($attribute->getKey());
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createValue(ATTR_TYPE $enum, DomainModel $domain, EntityModel $entity, AttributeModel $attribute, $value): ValueBase
    {
        $model = $enum->model();
        $model->setDomainKey($domain->getKey())
            ->setEntityKey($entity->getKey())
            ->setAttrKey($attribute->getKey())
            ->setValue($value);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function createEavEntity(array $fields, DomainModel $domain, AttributeSetModel $set): Result
    {
        $result = new Result();
        $result->created();
        $factory = new EntityFactory($this);
        $result->setData($factory->create($fields, $domain, $set));

        return $result;
    }
}
