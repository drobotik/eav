<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Content;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Trait\ImportContainerTrait;
use Drobotik\Eav\Trait\RepositoryTrait;
use Illuminate\Database\Eloquent\Collection;

class Worker
{
    use ImportContainerTrait;
    use RepositoryTrait;

    private AttributeSet  $attributeSet;
    private ValueSet      $valueSet;

    public function __construct() {
        $this->valueSet = new ValueSet();
        $this->attributeSet = new AttributeSet();
    }

    public function getValueSet(): ValueSet
    {
        return $this->valueSet;
    }

    public function getAttributeSet() : AttributeSet
    {
        return $this->attributeSet;
    }

    public function makeBulkValuesSet() : ValueSet
    {
        return new ValueSet();
    }

    public function parseCell(string $attributeName, $content, ?int $entityKey = null): void
    {
        $valueSet = $this->getValueSet();
        $attrSet = $this->getAttributeSet();
        $attribute = $attrSet->getAttribute($attributeName);
        $value = new Value();
        $value->setValue($content);
        $value->setType($attribute->getTypeEnum());
        $value->setAttributeKey($attribute->getKey());
        $value->setAttributeName($attribute->getName());
        if(!is_null($entityKey))
            $value->setEntityKey($entityKey);
        $valueSet->appendValue($value);
    }

    public function parseLine(array $line): void
    {
        $entityKey = key_exists(_ENTITY::ID->column(), $line) && !empty($line[_ENTITY::ID->column()])
            ? $line[_ENTITY::ID->column()]
            : null;

        if(!is_null($entityKey))
            unset($line[_ENTITY::ID->column()]);

        foreach($line as $name => $content)
        {
            $this->parseCell($name, $content, $entityKey);
        }
    }

    public function parseChunk(array $chunk): void
    {
        foreach ($chunk as $line)
        {
            $this->parseLine($line);
        }
    }

    public function createEntities() : Collection
    {
        $entityRepo = $this->makeEntityRepository();
        $container = $this->getContainer();
        $valueSet = $this->getValueSet();
        $values = $valueSet->forNewEntities();
        $domainKey = $container->getDomainKey();
        $setKey = $container->getSetKey();

        $amount = count($values);
        $serviceKey = $entityRepo->getServiceKey();
        $entityRepo->bulkCreate($amount, $domainKey, $setKey, $serviceKey);
        return $entityRepo->getByServiceKey($serviceKey);
    }

    public function processNewEntities(): void
    {
        $valueRepo = $this->makeValueRepository();
        $container = $this->getContainer();
        $valueSet = $this->getValueSet();
        $domainKey = $container->getDomainKey();
        $bulkCreateSet = $this->makeBulkValuesSet();
        /** @var EntityModel[] $entities */
        $entities = $this->createEntities();
        /**
         * @var Value $value
         */
        foreach($valueSet->forNewEntities() as $index => $value)
        {
            $value->setEntityKey($entities[$index]->getKey());
            $bulkCreateSet->appendValue($value);
        }
        $valueRepo->bulkCreate($bulkCreateSet, $domainKey);
    }

    public function processExistingEntities(): void
    {
        $container = $this->getContainer();
        $domainKey = $container->getDomainKey();
        $valueSet = $this->getValueSet();
        $attrSet = $this->getAttributeSet();
        $repository = $this->makeValueRepository();
        /**
         * @var Value $value
         */
        foreach($valueSet->forExistingEntities() as $value)
        {
            $attribute = $attrSet->getAttribute($value->getAttributeName());
            $repository->updateOrCreate(
                $domainKey,
                $value->getEntityKey(),
                $attribute->getKey(),
                $attribute->getTypeEnum(),
                $value->getValue()
            );
        }
    }

    public function cleanup(): void
    {
        $this->getValueSet()->resetValues();
    }

    public function run(): void
    {
        $container = $this->getContainer();
        $driver = $container->getDriver();
        $attrSet = $this->getAttributeSet();
        $attrSet->initialize();
        while (($chunk = $driver->getChunk()) !== null)
        {
            $this->parseChunk($chunk);
            $this->processExistingEntities();
            $this->processNewEntities();
            $this->cleanup();
        }
    }

}