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

use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\ATTR_FACTORY;
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
        $attributeRepo = $this->makeAttributeRepository();
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

            $attributeRecord = $attributeRepo->updateOrCreate($field[ATTR_FACTORY::ATTRIBUTE->field()], $domainKey);
            $attrKey = $attributeRecord->getKey();
            $type = $attributeRecord->getTypeEnum();
            $result->addAttribute($attributeRecord);

            $pivotRecord = $pivotModel->findOne($domainKey, $setKey, $field[ATTR_FACTORY::GROUP->field()], $attributeRecord->getKey());
            if($pivotRecord === false)
                $pivotKey = $factory->createPivot($domainKey, $setKey, $field[ATTR_FACTORY::GROUP->field()], $attributeRecord->getKey());
            else
                $pivotKey = $pivotRecord[_PIVOT::ID->column()];

            $result->addPivot($attributeRecord->getName(), $pivotKey);

            $valueRecord = isset($field[ATTR_FACTORY::VALUE->field()])
                ? $valueRepo->updateOrCreate($domainKey, $entityKey, $attrKey, $type, $field[ATTR_FACTORY::VALUE->field()])
                : null;

            if(!is_null($valueRecord)) {
                $result->addValue($attributeRecord->getName(), $valueRecord);
            }
        }

        return $result;
    }
}
