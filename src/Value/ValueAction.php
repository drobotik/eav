<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Value;

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Traits\ContainerTrait;
use Drobotik\Eav\Traits\SingletonsTrait;

class ValueAction
{
    use SingletonsTrait;
    use ContainerTrait;

    public function create(): Result
    {
        $result = new Result();

        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $valueManager = $container->getValueManager();
        $valueModel = $this->makeValueModel();
        $parser = $this->makeValueParser();
        if (!$valueManager->isRuntime()) {
            return $result->empty();
        }

        $type = $attribute->getType();
        $value = $parser->parse($type, $valueManager->getRuntime());

        $valueKey = $valueModel->create(
            $type,
            $entity->getDomainKey(),
            $entity->getKey(),
            $attribute->getKey(),
            $value
        );

        $valueManager->setStored($value)
            ->setKey($valueKey)
            ->clearRuntime();

        return $result->created();
    }

    public function find(): Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $type = $attribute->getType();
        $attributeKey = $attribute->getKey();
        $entity = $container->getAttributeSet()->getEntity();

        $valueManager = $container->getValueManager();
        $valueModel = $this->makeValueModel();

        if (is_null($attributeKey) || !$entity->hasKey()) {
            return $result->empty();
        }

        $entityKey = $entity->getKey();
        $domainKey = $entity->getDomainKey();

        $record = $valueModel->find(
            $type,
            $domainKey,
            $entityKey,
            $attributeKey
        );

        if ($record === false) {
            return $result->notFound();
        }

        $valueManager
            ->setKey($record[_VALUE::ID])
            ->setStored($record[_VALUE::VALUE]);

        return $result->found();
    }

    public function update(): Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $entity = $container->getAttributeSet()->getEntity();
        $attribute = $container->getAttribute();
        $type = $attribute->getType();
        $valueManager = $container->getValueManager();
        $valueModel = $this->makeValueModel();
        $valueParser = $this->makeValueParser();

        if (!$valueManager->isRuntime()) {
            return $result->empty();
        }

        $domainKey = $entity->getDomainKey();
        $entityKey = $entity->getKey();
        $attributeKey = $attribute->getKey();

        $value = $valueParser->parse($type, $valueManager->getRuntime());

        $valueModel->update(
            $type,
            $domainKey,
            $entityKey,
            $attributeKey,
            $value
        );

        $valueManager->setStored($value)
            ->clearRuntime();

        return $result->updated();
    }

    public function delete(): Result
    {
        $result = new Result();
        $container = $this->getAttributeContainer();
        $entity = $container->getAttributeSet()->getEntity();
        $attribute = $container->getAttribute();
        $type = $attribute->getType();
        $valueManager = $container->getValueManager();
        $valueModel = $this->makeValueModel();

        if (!$valueManager->hasKey()) {
            return $result->empty();
        }

        $domainKey = $entity->getDomainKey();
        $entityKey = $entity->getKey();
        $attributeKey = $attribute->getKey();

        $deleted = $valueModel->destroy($type, $domainKey, $entityKey, $attributeKey);

        if ($deleted === 0) {
            return $result->notDeleted();
        }

        $valueManager->clearStored()
            ->clearRuntime()
            ->setKey(0);

        return $result->deleted();
    }
}
