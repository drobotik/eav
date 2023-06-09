<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

use Drobotik\Eav\Trait\ImportContainerTrait;
use Drobotik\Eav\Trait\SingletonsTrait;

class Worker
{
    use SingletonsTrait;
    use ImportContainerTrait;

    private Config          $config;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function isConfig() : bool
    {
        return isset($this->config);
    }

    public function getValidator(): Validator
    {
        $validator = new Validator();
        $validator->setWorker($this);
        return $validator;
    }

    public function validate(): void
    {
        $config = $this->getConfig();
        $validator = $this->getValidator();
        $validator->setConfig($config);
        $validator->validate();
    }

    public function createAttribute(ConfigAttribute $attribute): void
    {
        $factory = $this->makeEavFactory();
        $pivotModel = $this->makePivotModel();
        $container = $this->getContainer();
        $domainKey = $container->getDomainKey();
        $setKey = $container->getSetKey();
        $groupKey = $attribute->getGroupKey();
        $attrKey = $factory->createAttribute($domainKey, $attribute->getFields());
        $pivotRecord = $pivotModel->findOne($domainKey, $setKey, $groupKey, $attrKey);
        if($pivotRecord === false)
            $factory->createPivot($domainKey, $setKey, $groupKey, $attrKey);
    }

    public function createAttributes(): void
    {
        $config = $this->getConfig();
        foreach ($config->getAttributes() as $attribute)
        {
            $this->createAttribute($attribute);
        }
    }

    public function run(): void
    {
        $this->validate();
        $this->createAttributes();
    }
}