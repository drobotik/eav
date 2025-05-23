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
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Result\Result;
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
            _DOMAIN::NAME => $this->faker->word(),
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
            _ENTITY::DOMAIN_ID => $domainKey,
            _ENTITY::ATTR_SET_ID => $setKey
        ]);
    }

    public function createAttributeSet(?int $domainKey = null, array $data = []): int
    {
        if (is_null($domainKey)) {
            $domainKey = $this->createDomain();
        }
        $defaultData = [
            _SET::NAME => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeSetModel();
        return $model->create([
            _SET::DOMAIN_ID => $domainKey,
            _SET::NAME => $input[_SET::NAME]
        ]);
    }

    public function createGroup(?int $setKey = null, array $data = []): int
    {
        if (is_null($setKey)) {
            $setKey = $this->createAttributeSet();
        }
        $defaultData = [
            _GROUP::SET_ID => $setKey,
            _GROUP::NAME => $this->faker->word(),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeGroupModel();

        return $model->create([
            _GROUP::SET_ID => $setKey,
            _GROUP::NAME => $input[_SET::NAME]
        ]);
    }

    public function createAttribute(?int $domainKey = null, array $data = []): int
    {
        if (is_null($domainKey)) {
            $domainKey = $this->createDomain();
        }
        $defaultData = [
            _ATTR::DOMAIN_ID => $domainKey,
            _ATTR::NAME => $this->faker->slug(2),
            _ATTR::TYPE => _ATTR::bag(_ATTR::TYPE),
            _ATTR::STRATEGY => _ATTR::bag(_ATTR::STRATEGY),
            _ATTR::SOURCE => _ATTR::bag(_ATTR::SOURCE),
            _ATTR::DEFAULT_VALUE => _ATTR::bag(_ATTR::DEFAULT_VALUE),
            _ATTR::DESCRIPTION => _ATTR::bag(_ATTR::DESCRIPTION),
        ];
        $input = array_merge($defaultData, $data);
        $model = new AttributeModel();
        return $model->create([
            _ATTR::DOMAIN_ID => $domainKey,
            _ATTR::NAME => $input[_ATTR::NAME],
            _ATTR::TYPE => $input[_ATTR::TYPE],
            _ATTR::STRATEGY => $input[_ATTR::STRATEGY],
            _ATTR::SOURCE => $input[_ATTR::SOURCE],
            _ATTR::DEFAULT_VALUE => $input[_ATTR::DEFAULT_VALUE],
            _ATTR::DESCRIPTION => $input[_ATTR::DESCRIPTION],
        ]);
    }

    public function createPivot(int $domainKey, int $setKey, int $groupKey, int $attributeKey): int
    {
        $model = new PivotModel();
        return $model->create([
            _PIVOT::DOMAIN_ID => $domainKey,
            _PIVOT::SET_ID => $setKey,
            _PIVOT::GROUP_ID => $groupKey,
            _PIVOT::ATTR_ID => $attributeKey
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
