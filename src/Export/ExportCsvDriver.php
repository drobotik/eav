<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Export;

use Drobotik\Eav\DomainDataDriver;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\EXPORT;
use Drobotik\Eav\Exception\AttributeTypeException;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Result\Result;

class ExportCsvDriver extends DomainDataDriver
{

    /**
     * @throws AttributeTypeException
     */
    public function run(): Result
    {
        $domainKey = $this->getField(EXPORT::DOMAIN_KEY->field());
        $setKey = $this->getField(EXPORT::SET_KEY->field());
        $path = $this->getField(EXPORT::PATH->field());
        $fp = fopen($path, 'w');

        $pt = _PIVOT::table();
        $at = _ATTR::table();
        $attributes = AttributeModel::query()
            ->join($pt, $pt.'.'._PIVOT::ATTR_ID->column(), '=', $at.'.'._ATTR::ID->column())
            ->where($pt. '.'._PIVOT::SET_ID->column(), '=', $setKey)
            ->where($pt. '.'._PIVOT::DOMAIN_ID->column(), '=', $domainKey)
            ->get();

        $header = $attributes->pluck(_ATTR::NAME->column())->toArray();
        fputcsv($fp, $header);

        $entities = EntityModel::query()
            ->where(_ENTITY::ATTR_SET_ID->column(), $setKey)
            ->where(_ENTITY::DOMAIN_ID->column(), $domainKey)
            ->get();
        /** @var EntityModel $entityRecord */
        foreach($entities as $entityRecord) {
            $row = [];
            /** @var AttributeModel $attribute */
            foreach($attributes as $attribute) {
                /** @var ValueBase $value */
                $value = ATTR_TYPE::getCase($attribute->getType())
                    ->model()
                    ->query()
                    ->where(_VALUE::DOMAIN_ID->column(), $domainKey)
                    ->where(_VALUE::ENTITY_ID->column(), $entityRecord->getKey())
                    ->where(_VALUE::ATTRIBUTE_ID->column(), $attribute->getKey())
                    ->first();
                $row[] = is_null($value)
                    ? ''
                    : $value->getValue();
            }
            fputcsv($fp, $row);
        }
        fclose($fp);
        $result = new Result();
        $result->exportSuccess();

        return $result;
    }
}
