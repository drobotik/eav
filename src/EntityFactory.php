<?php

namespace Drobotik\Eav;

use Drobotik\Dev\EavFactory;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\EntityFactoryException;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Result\EntityFactoryResult;

class EntityFactory
{
    private EavFactory $eavFactory;

    public function __construct(EavFactory $eavFactory)
    {
        $this->eavFactory = $eavFactory;
    }

    private function checkGroups(AttributeSetModel $set, array $fields) {
        $groupIds = array_flip(array_unique(array_column($fields, ATTR_FACTORY::GROUP->field())));
        $groupKeys = AttributeGroupModel::query()
            ->where(_GROUP::SET_ID->column(), $set->getKey())
            ->pluck(_GROUP::NAME->column(), _GROUP::ID->column());
        $groupDiff = array_diff_key($groupIds, $groupKeys->toArray());
        if (count($groupDiff) > 0)   {
            $output = [];
            foreach ($groupDiff as $key => $value) {
                $output[] = $key . " => " . $value;
            }
            throw new EntityFactoryException("Groups not found: " . implode(", ", $output));
        }
    }

    private function initAttribute(array $field, DomainModel $domain) {
        if(!key_exists(ATTR_FACTORY::ATTRIBUTE->field(), $field)) {
            throw new EntityFactoryException("Attribute not found");
        }
        $data = $field[ATTR_FACTORY::ATTRIBUTE->field()];
        if(!key_exists(_ATTR::NAME->column(), $data)) {
            throw new EntityFactoryException("Attribute name not found");
        }
        if(!key_exists(_ATTR::TYPE->column(), $data)) {
            throw new EntityFactoryException("Attribute type not found");
        }
        $name = $data[_ATTR::NAME->column()];
        $attribute = AttributeModel::query()
            ->where(_ATTR::NAME->column(), $name)
            ->where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->first();
        if (is_null($attribute)) {
            $attribute = $this->eavFactory->createAttribute($domain, $data);
        }
        return $attribute;
    }

    private function initPivot(DomainModel $domain, AttributeSetModel $set, AttributeModel $attributeModel, int $groupKey) : PivotModel
    {
        $pivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $attributeModel->getKey())
            ->first();

        if(is_null($pivot)) {
            $group = AttributeGroupModel::whereKey($groupKey)->firstOrFail();
            $pivot = $this->eavFactory->createPivot($domain, $set, $group, $attributeModel);
        }

        return $pivot;
    }

    private function initValue(DomainModel $domain, EntityModel $entity, AttributeModel $attribute, array $field) {
        if(!key_exists(ATTR_FACTORY::VALUE->field(), $field)) {
            throw new EntityFactoryException("Value not found");
        }
        $valueType = ATTR_TYPE::getCase($attribute->getType());
        $value = $field[ATTR_FACTORY::VALUE->field()];
        $model = $valueType->model();
        $valueRecord = $model::query()
            ->where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attribute->getKey())
            ->first();
        if(is_null($valueRecord)) {
            $valueRecord = $this->eavFactory->createValue($valueType, $domain, $entity, $attribute, $value);
        } else {
            $valueRecord->setVaue($field[ATTR_FACTORY::VALUE->field()]);
            $valueRecord->save();
        }
        return $valueRecord;
    }

    public function create(array $fields, DomainModel $domain, AttributeSetModel $set) : EntityFactoryResult
    {
        $result = new EntityFactoryResult;
        $this->checkGroups($set, $fields);
        $entityRecord = $this->eavFactory->createEntity($domain, $set);
        $result->setEntityModel($entityRecord);
        foreach ($fields as $field) {
            $attributeRecord = $this->initAttribute($field, $domain);
            $result->addAttribute($attributeRecord);
            $pivotRecord = $this->initPivot($domain, $set, $attributeRecord, $field[ATTR_FACTORY::GROUP->field()]);
            $result->addPivot($attributeRecord->getName(), $pivotRecord);
            $valueRecord = $this->initValue($domain, $entityRecord, $attributeRecord, $field);
            $result->addValue($attributeRecord->getName(), $valueRecord);
        }
        return $result;
    }
}