<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Content;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Trait\ImportContainerTrait;
use Drobotik\Eav\Trait\SingletonsTrait;

class Worker
{
    use ImportContainerTrait;
    use SingletonsTrait;

    private AttributeSet  $attributeSet;
    private ValueSet      $valueSet;
    private int $lineIndex;

    public function __construct() {
        $this->valueSet = new ValueSet();
        $this->attributeSet = new AttributeSet();
        $this->attributeSet->setWorker($this);
        $this->resetLineIndex();
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

    public function incrementLineIndex(): void
    {
        $this->lineIndex++;
    }
    public function getLineIndex(): int
    {
        return $this->lineIndex;
    }
    public function resetLineIndex() : void
    {
        $this->lineIndex = 0;
    }

    public function parseCell(string $attributeName, $content, ?int $entityKey = null): void
    {
        $valueSet = $this->getValueSet();
        $attrSet = $this->getAttributeSet();
        $attribute = $attrSet->getAttribute($attributeName);
        $value = new Value();
        $value->setType(ATTR_TYPE::getCase($attribute[_ATTR::TYPE->column()]));
        $value->setValue($content);
        $value->setAttributeKey($attribute[_ATTR::ID->column()]);
        $value->setAttributeName($attribute[_ATTR::NAME->column()]);
        if(!is_null($entityKey))
        {
            $value->setEntityKey($entityKey);
        } else
        {
            $value->setLineIndex($this->getLineIndex());
        }

        $valueSet->appendValue($value);
    }

    public function parseLine(array $line): void
    {
        if(!key_exists(_ENTITY::ID->column(), $line))
        {
            EntityException::mustBeEntityKey();
        }

        $entityKey = empty($line[_ENTITY::ID->column()])
            ? null
            : (int) $line[_ENTITY::ID->column()];
        unset($line[_ENTITY::ID->column()]);

        if ($entityKey < 1)
        {
            $this->incrementLineIndex();
        }

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

    public function createEntities() : array|bool
    {
        $model = $this->makeEntityModel();
        $container = $this->getContainer();

        $domainKey = $container->getDomainKey();
        $setKey = $container->getSetKey();

        $amount = $this->getLineIndex();
        $serviceKey = $model->getServiceKey();

        $model->bulkCreate($amount, $domainKey, $setKey, $serviceKey);
        return $model->getByServiceKey($serviceKey);
    }

    public function processNewEntities(): void
    {
        $valueModel = $this->makeValueModel();
        $container = $this->getContainer();
        $valueSet = $this->getValueSet();
        $domainKey = $container->getDomainKey();
        $bulkCreateSet = $this->makeBulkValuesSet();
        $entities = $this->createEntities();
        /**
         * @var Value $value
         */
        foreach($valueSet->forNewEntities() as $value)
        {
            if($value->isEmptyValue()) continue;
            $value->setEntityKey($entities[$value->getLineIndex() - 1][_ENTITY::ID->column()]);
            $bulkCreateSet->appendValue($value);
        }
        $valueModel->bulkCreate($bulkCreateSet, $domainKey);
    }

    public function processExistingEntities(): void
    {
        $container = $this->getContainer();
        $domainKey = $container->getDomainKey();
        $valueSet = $this->getValueSet();
        $attrSet = $this->getAttributeSet();
        $valueModel = $this->makeValueModel();
        $valueParser = $this->makeValueParser();
        /**
         * @var Value $value
         */
        foreach($valueSet->forExistingEntities() as $attributeValue)
        {
            $value = $attributeValue->getValue();
            $entityKey = $attributeValue->getEntityKey();
            $attribute = $attrSet->getAttribute($attributeValue->getAttributeName());
            $attributeKey = $attribute[_ATTR::ID->column()];
            $attributeType = ATTR_TYPE::getCase($attribute[_ATTR::TYPE->column()]);
            $valueTable = $attributeType->valueTable();
            if($value == '')
                $valueModel->destroy($valueTable, $domainKey, $entityKey, $attributeKey);
            else
            {
                $value = $valueParser->parse($attributeType, $value);
                $record = $valueModel->find($valueTable, $domainKey, $entityKey, $attributeKey);
                if($record === false)
                {
                    $valueModel->create($valueTable, $domainKey, $entityKey, $attributeKey, $value);
                } else
                {
                    $valueModel->update($valueTable, $domainKey, $entityKey, $attributeKey, $value);
                }
            }
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
            $this->resetLineIndex();
        }
    }

}