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
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeTypeException;
use Drobotik\Eav\Exception\EntityFactoryException;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Result\EntityFactoryResult;

class EntityFactory
{
    private EavFactory $eavFactory;

    public function __construct(EavFactory $eavFactory)
    {
        $this->eavFactory = $eavFactory;
    }

    public function create(array $fields, DomainModel $domain, AttributeSetModel $set): EntityFactoryResult
    {
        $result = new EntityFactoryResult();
        $this->checkGroups($set, $fields);
        $entityRecord = $this->eavFactory->createEntity($domain, $set);
        $result->setEntityModel($entityRecord);
        foreach ($fields as $field) {
            $attributeRecord = $this->initAttribute($field, $domain);
            $result->addAttribute($attributeRecord);
            $pivotRecord = $this->initPivot($domain, $set, $attributeRecord, $field[ATTR_FACTORY::GROUP->field()]);
            $result->addPivot($attributeRecord->getName(), $pivotRecord);
            $valueRecord = $this->initValue($domain, $entityRecord, $attributeRecord, $field);
            if(!is_null($valueRecord)) {
                $result->addValue($attributeRecord->getName(), $valueRecord);
            }
        }

        return $result;
    }

    private function entityBulk(int $amount, int $domainKey, int $setKey) : int
    {
        $time = time();
        $format = "($domainKey, $setKey, $time)";
        $bulk = [];
        for($i=0;$i<$amount;$i++) {
            $bulk[] = $format;
        }
        $template = sprintf(
            "INSERT INTO "._ENTITY::table()." ("._ENTITY::DOMAIN_ID->column().", "._ENTITY::ATTR_SET_ID->column().", "._ENTITY::BULK_INSERT_ID->column().") VALUES %s;",
            implode(',',$bulk)
        );

        $path = dirname(__DIR__, 2) . '/tests/test.sqlite';
        $pdo = new \PDO("sqlite:$path");

        $pdo->exec($template);
        return $time;
    }

    private function valuesBulk(array $entities, array $values, array $attributes, int $domainKey)
    {

        $path = dirname(__DIR__, 2) . '/tests/test.sqlite';
        $pdo = new \PDO("sqlite:$path");

        $template = "INSERT INTO %s ("._VALUE::DOMAIN_ID->column().","._VALUE::ENTITY_ID->column().","._VALUE::ATTRIBUTE_ID->column().","._VALUE::VALUE->column().")";

        $stringTable = ATTR_TYPE::STRING->valueTable();
        $integerTable = ATTR_TYPE::INTEGER->valueTable();
        $decimalTable = ATTR_TYPE::DECIMAL->valueTable();
        $datetimeTable = ATTR_TYPE::DATETIME->valueTable();
        $textTable = ATTR_TYPE::TEXT->valueTable();

        $stringTemplate = sprintf($template, $stringTable) . " VALUES %s;";
        $integerTemplate = sprintf($template, $integerTable) . " VALUES %s;";
        $decimalTemplate = sprintf($template, $decimalTable) . " VALUES %s;";
        $datetimeTemplate = sprintf($template, $datetimeTable) . " VALUES %s;";
        $textTemplate = sprintf($template, $textTable) . " VALUES %s;";

        $stringBulk = [];
        $integerBulk = [];
        $decimalBulk = [];
        $datetimeBulk = [];
        $textBulk = [];

        foreach($entities as $index => $entityKey) {
            $line = $values[$index];
            foreach ($line as $data) {
                $attributeKey = $attributes[$data["attribute"]];
                $value = $data["value"];
                $table = $data["table"];
                $bulkTemplate = "($domainKey, $entityKey, $attributeKey, '$value')";
                match($table) {
                    $stringTable => $stringBulk[] = $bulkTemplate,
                    $integerTable => $integerBulk[] = $bulkTemplate,
                    $decimalTable => $decimalBulk[] = $bulkTemplate,
                    $datetimeTable=> $datetimeBulk[] = $bulkTemplate,
                    $textTable    => $textBulk[] = $bulkTemplate
                };
            }
        }

        if(count($stringBulk) > 0) {
            $pdo->exec(sprintf($stringTemplate, implode(',', $stringBulk)));
        }
        if(count($integerBulk) > 0) {
            $pdo->exec(sprintf($integerTemplate, implode(',', $integerBulk)));
        }
        if(count($decimalBulk) > 0) {
            $pdo->exec(sprintf($decimalTemplate, implode(',', $decimalBulk)));
        }
        if(count($datetimeBulk) > 0) {
            $pdo->exec(sprintf($datetimeTemplate, implode(',', $datetimeBulk)));
        }
        if(count($textBulk) > 0) {
            $pdo->exec(sprintf($textTemplate, implode(',', $textBulk)));
        }
    }

    public function bulkCreate(array $config, DomainModel $domain, AttributeSetModel $set) {
        $start = microtime(true);
        $attributes = [];
        foreach ($config["attributes"] as $attribute) {
            $attributeRecord = $this->initAttribute($attribute, $domain);
            $this->initPivot($domain, $set, $attributeRecord, $attribute[ATTR_FACTORY::GROUP->field()]);
            $attributes[$attributeRecord->getName()] = $attributeRecord->getKey();
        }
        $end = microtime(true);
        print_r("Attrs created:". $end-$start. "\n");

        $amount = count($config['values']);

        $this->checkGroups($set, $config["attributes"]);

        $start = microtime(true);
        $bulkID = $this->entityBulk($amount, $domain->getKey(), $set->getKey());

        $entities = EntityModel::query()->where(_ENTITY::BULK_INSERT_ID->column(), $bulkID)->pluck(_ENTITY::ID->column())->toArray();
        $end = microtime(true);
        print_r("Entities created:". $end-$start. "\n");

        $start = microtime(true);
        $this->valuesBulk($entities, $config["values"], $attributes, $domain->getKey());
        $end = microtime(true);
        print_r("Values created:". $end-$start);
    }

    private function checkGroups(AttributeSetModel $set, array $fields)
    {
        foreach($fields as $field) {
            if (!key_exists(ATTR_FACTORY::GROUP->field(), $field)) {
                throw new EntityFactoryException("Group key must be provided!");
            }
        }

        $groupIds = array_flip(array_unique(array_column($fields, ATTR_FACTORY::GROUP->field())));
        $groupKeys = AttributeGroupModel::query()
            ->where(_GROUP::SET_ID->column(), $set->getKey())
            ->pluck(_GROUP::NAME->column(), _GROUP::ID->column());

        $groupDiff = array_diff_key($groupIds, $groupKeys->toArray());
        if (count($groupDiff) > 0) {
            throw new EntityFactoryException('Groups not found');
        }
    }

    /**
     * @throws EntityFactoryException
     * @throws AttributeTypeException
     */
    private function initAttribute(array $field, DomainModel $domain)
    {
        if (!key_exists(ATTR_FACTORY::ATTRIBUTE->field(), $field)) {
            EntityFactoryException::undefinedAttributeArray();
        }
        $data = $field[ATTR_FACTORY::ATTRIBUTE->field()];
        if (!key_exists(_ATTR::NAME->column(), $data)) {
            EntityFactoryException::undefinedAttributeName();
        }
        if (!key_exists(_ATTR::TYPE->column(), $data)) {
            EntityFactoryException::undefinedAttributeType();
        }
        // check type is supported
        ATTR_TYPE::getCase($data[_ATTR::TYPE->column()]);

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

    private function initPivot(DomainModel $domain, AttributeSetModel $set, AttributeModel $attributeModel, int $groupKey): PivotModel
    {
        $pivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $attributeModel->getKey())
            ->first();

        if (is_null($pivot)) {
            $group = AttributeGroupModel::whereKey($groupKey)->firstOrFail();
            $pivot = $this->eavFactory->createPivot($domain, $set, $group, $attributeModel);
        }

        return $pivot;
    }

    private function initValue(DomainModel $domain, EntityModel $entity, AttributeModel $attribute, array $field) : ?ValueBase
    {
        $value = key_exists(ATTR_FACTORY::VALUE->field(), $field)
            ? $field[ATTR_FACTORY::VALUE->field()]
            : null;
        if(is_null($value)) {
            return null;
        }
        $type = ATTR_TYPE::getCase($attribute->getType());
        $model = $type->model();
        $record = $model::query()
            ->where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attribute->getKey())
            ->first();
        if (is_null($record)) {
            $record = $this->eavFactory->createValue($type, $domain, $entity, $attribute, $value);
        } else {
            $record->setVaue($field[ATTR_FACTORY::VALUE->field()]);
            $record->save();
        }

        return $record;
    }
}
