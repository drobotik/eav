<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Model\AttributeModel;
use Illuminate\Support\Collection;

class QueryBuilderAttributes
{
    /** @var AttributeModel[] */
    private array $attributes = [];

    private array $joins = [];

    private array $selected = [];

    public function setAttributes(Collection $attributes) : void
    {
        $attributes->each(fn(AttributeModel $attr) => $this->appendAttribute($attr));
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function appendAttribute(AttributeModel $attributeModel): void
    {
        $this->attributes[$attributeModel->getName()] = $attributeModel;
    }

    public function getAttribute(string $name) : AttributeModel
    {
        return $this->attributes[$name];
    }

    public function isAttribute(string $name) : bool
    {
        return key_exists($name, $this->attributes);
    }

    public function setAttributeJoined(string $name): void
    {
        if(!$this->isAttribute($name)) return;
        if(!$this->isAttributeJoined($name))
        {
            $this->joins[] = $name;
        }
    }

    public function isAttributeJoined(string $name) : bool
    {
        return in_array($name, $this->joins);
    }

    public function setAttributeSelected(string $name): void
    {
        if(!$this->isAttribute($name)) return;
        if(!$this->isAttributeJoined($name))
        {
            $this->selected[] = $name;
        }
    }

    public function isAttributeSelected(string $name) : bool
    {
        return in_array($name, $this->selected);
    }
}