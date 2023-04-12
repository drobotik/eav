<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Result;

use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueBase;

class EntityFactoryResult
{
    private EntityModel $entityModel;
    /** @var PivotModel[] */
    private array $pivots = [];
    /** @var AttributeModel[] */
    private array $attributes = [];
    /** @var ValueBase[] */
    private array $values = [];

    public function setEntityModel(EntityModel $entityModel): void
    {
        $this->entityModel = $entityModel;
    }

    public function getEntityModel(): EntityModel
    {
        return $this->entityModel;
    }

    public function addAttribute(AttributeModel $attributeModel): void
    {
        $this->attributes[$attributeModel->getName()] = $attributeModel;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addValue(string $attrName, ValueBase $valueModel): void
    {
        $this->values[$attrName] = $valueModel;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function addPivot(string $attrName, PivotModel $pivot): void
    {
        $this->pivots[$attrName] = $pivot;
    }

    public function getPivots() : array
    {
        return $this->pivots;
    }
}