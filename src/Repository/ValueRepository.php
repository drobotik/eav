<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Model\ValueBase;

class ValueRepository extends BaseRepository
{

    public function updateOrCreate(int $domainKey, int $entityKey, int $attributeKey, ATTR_TYPE $type, mixed $value) : ValueBase
    {
        $model = $type->model();
        $query = $model->newQuery()
            ->where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributeKey);
        $record = $query->first();

        if(!is_null($record))
        {
            $record->setValue($value);
            $record->save();
        }
        else
        {
            $factory = $this->getFactory();
            $record = $factory->createValue($type, $domainKey, $entityKey, $attributeKey, $value);
        }

        return $record;
    }

    public function bulkCreate(ValueSet $valueSet, int $domainKey): void
    {
        $pdo = Connection::pdo();

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

        foreach ($valueSet->forExistingEntities() as $data) {
            $attributeKey = $data->getAttributeKey();
            $value = $data->getValue();
            $table = $data->getType()->valueTable();
            $entityKey = $data->getEntityKey();
            $bulkTemplate = "($domainKey, $entityKey, $attributeKey, '$value')";
            match($table) {
                $stringTable => $stringBulk[] = $bulkTemplate,
                $integerTable => $integerBulk[] = $bulkTemplate,
                $decimalTable => $decimalBulk[] = $bulkTemplate,
                $datetimeTable=> $datetimeBulk[] = $bulkTemplate,
                $textTable    => $textBulk[] = $bulkTemplate
            };
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
}