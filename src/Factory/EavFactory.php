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
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Result\Result;
use Faker\Generator;

class EavFactory
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function createDomain(array $data = []): int
    {
        $defaultData = [
            _DOMAIN::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new DomainModel();
        return $model->create($input);
    }

    public function createEntity(?int $domainKey = null, ?int $setKey = null): int
    {
        if (is_null($domainKey))
        {
            $domainKey = $this->createDomain();
        }
        if (is_null($setKey))
        {
            $setKey = $this->createAttributeSet($domainKey);
        }
        $model = new EntityModel();
        return $model->create([
            _ENTITY::DOMAIN_ID->column() => $domainKey,
            _ENTITY::ATTR_SET_ID->column() => $setKey
        ]);
    }

    public function createAttributeSet(?int $domainKey = null, array $data = []): int
    {
        if (is_null($domainKey)) {
            $domainKey = $this->createDomain();
        }
        $defaultData = [
            _SET::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeSetModel();
        return $model->create([
            _SET::DOMAIN_ID->column() => $domainKey,
            _SET::NAME->column() => $input[_SET::NAME->column()]
        ]);
    }

    public function createGroup(?int $setKey = null, array $data = []): int
    {
        if (is_null($setKey)) {
            $setKey = $this->createAttributeSet();
        }
        $defaultData = [
            _GROUP::SET_ID->column() => $setKey,
            _GROUP::NAME->column() => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeGroupModel();

        return $model->create([
            _GROUP::SET_ID->column() => $setKey,
            _GROUP::NAME->column() => $input[_SET::NAME->column()]
        ]);
    }

    public function createAttribute(?int $domainKey = null, array $data = []): int
    {
        if (is_null($domainKey)) {
            $domainKey = $this->createDomain();
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
        return $model->create([
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => $input[_ATTR::NAME->column()],
            _ATTR::TYPE->column() => $input[_ATTR::TYPE->column()],
            _ATTR::STRATEGY->column() => $input[_ATTR::STRATEGY->column()],
            _ATTR::SOURCE->column() => $input[_ATTR::SOURCE->column()],
            _ATTR::DEFAULT_VALUE->column() => $input[_ATTR::DEFAULT_VALUE->column()],
            _ATTR::DESCRIPTION->column() => $input[_ATTR::DESCRIPTION->column()],
        ]);
    }

    public function createPivot(int $domainKey, int $setKey, int $groupKey, int $attributeKey): int
    {
        $model = new PivotModel();
        return $model->create([
            _PIVOT::DOMAIN_ID->column() => $domainKey,
            _PIVOT::SET_ID->column() => $setKey,
            _PIVOT::GROUP_ID->column() => $groupKey,
            _PIVOT::ATTR_ID->column() => $attributeKey
        ]);
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
