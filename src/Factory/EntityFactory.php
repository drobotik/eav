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
        $pivotRepo = $this->makePivotRepository();
        $valueRepo = $this->makeValueRepository();
        $attributeRepo = $this->makeAttributeRepository();

        $groupRepo = $this->makeGroupRepository();
        foreach($fields as $field) {
            if (!key_exists(ATTR_FACTORY::GROUP->field(), $field)) {
                throw new EntityFactoryException("Group key must be provided!");
            }
        }
        $groupIds = array_flip(array_unique(array_column($fields, ATTR_FACTORY::GROUP->field())));
        $groupRepo->checkIsBelongsTo($setKey, $groupIds);

        $entityRecord = $this->makeEavFactory()->createEntity($domainKey, $setKey);
        $result->setEntityModel($entityRecord);

        foreach ($fields as $field) {

            if (!key_exists(ATTR_FACTORY::ATTRIBUTE->field(), $field)) {
                EntityFactoryException::undefinedAttributeArray();
            }

            $attributeRecord = $attributeRepo->updateOrCreate($field[ATTR_FACTORY::ATTRIBUTE->field()], $domainKey);
            $attrKey = $attributeRecord->getKey();
            $type = $attributeRecord->getTypeEnum();
            $result->addAttribute($attributeRecord);
            $pivotRecord = $pivotRepo->createIfNotExist($domainKey, $setKey, $field[ATTR_FACTORY::GROUP->field()], $attributeRecord->getKey());
            $result->addPivot($attributeRecord->getName(), $pivotRecord);

            $valueRecord = isset($field[ATTR_FACTORY::VALUE->field()])
                ? $valueRepo->updateOrCreate($domainKey, $entityRecord->getKey(), $attrKey, $type, $field[ATTR_FACTORY::VALUE->field()])
                : null;

            if(!is_null($valueRecord)) {
                $result->addValue($attributeRecord->getName(), $valueRecord);
            }
        }

        return $result;
    }
}
