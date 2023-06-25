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
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Exception\EntityFactoryException;
use Drobotik\Eav\Result\EntityFactoryResult;
use Drobotik\Eav\Trait\RepositoryTrait;
use Drobotik\Eav\Trait\SingletonsTrait;

class EntityFactory
{
    use RepositoryTrait;
    use SingletonsTrait;

    public function create(array $fields, int $domainKey, int $setKey): EntityFactoryResult
    {
        $result = new EntityFactoryResult();
        $valueRepo = $this->makeValueRepository();
        $attributeModel = $this->makeAttributeModel();
        $groupModel = $this->makeGroupModel();
        $pivotModel = $this->makePivotModel();
        $factory = $this->makeEavFactory();
        foreach($fields as $field) {
            if (!key_exists(ATTR_FACTORY::GROUP->field(), $field)) {
                throw new EntityFactoryException("Group key must be provided!");
            }
        }
        $groups = array_unique(array_column($fields, ATTR_FACTORY::GROUP->field()));
        foreach($groups as $groupKey)
        {
            if(!$groupModel->checkGroupInAttributeSet($setKey, $groupKey))
            {
                throw new EntityFactoryException("This group is not belongs to attribute set");
            }
        }

        $entityKey = $this->makeEavFactory()->createEntity($domainKey, $setKey);
        $result->setEntityKey($entityKey);

        foreach ($fields as $field) {

            if (!key_exists(ATTR_FACTORY::ATTRIBUTE->field(), $field)) {
                EntityFactoryException::undefinedAttributeArray();
            }
            $attrConfig = $field[ATTR_FACTORY::ATTRIBUTE->field()];
            if (!key_exists(_ATTR::NAME->column(), $attrConfig)) {
                AttributeException::undefinedAttributeName();
            }
            if (!key_exists(_ATTR::TYPE->column(), $attrConfig)) {
                AttributeException::undefinedAttributeType();
            }

            $attrName = $attrConfig[_ATTR::NAME->column()];
            $attrType = ATTR_TYPE::getCase($attrConfig[_ATTR::TYPE->column()]);
            $attrRecord = $attributeModel->findByName($attrConfig[_ATTR::NAME->column()], $domainKey);

            if ($attrRecord !== false) {
                $attrKey = $attrRecord[_ATTR::ID->column()];
                $attrData = array_intersect_key([
                    _ATTR::NAME->column() => null,
                    _ATTR::TYPE->column() => null,
                    _ATTR::STRATEGY->column() => null,
                    _ATTR::SOURCE->column() => null,
                    _ATTR::DEFAULT_VALUE->column() => null,
                    _ATTR::DESCRIPTION->column() => null
                ], $attrConfig);
                $attributeModel->update($attrKey, $attrData);
            } else {
                $attrKey = $factory->createAttribute($domainKey, $attrConfig);
            }

            $result->addAttribute([
                _ATTR::ID->column() => $attrKey,
                _ATTR::NAME->column() => $attrConfig[_ATTR::NAME->column()]
            ]);

            $pivotRecord = $pivotModel->findOne($domainKey, $setKey, $field[ATTR_FACTORY::GROUP->field()], $attrKey);
            if($pivotRecord === false)
                $pivotKey = $factory->createPivot($domainKey, $setKey, $field[ATTR_FACTORY::GROUP->field()], $attrKey);
            else
                $pivotKey = $pivotRecord[_PIVOT::ID->column()];

            $result->addPivot($attrName, $pivotKey);

            $valueRecord = isset($field[ATTR_FACTORY::VALUE->field()])
                ? $valueRepo->updateOrCreate($domainKey, $entityKey, $attrKey, $attrType, $field[ATTR_FACTORY::VALUE->field()])
                : null;

            if(!is_null($valueRecord)) {
                $result->addValue($attrName, $valueRecord);
            }
        }

        return $result;
    }
}
