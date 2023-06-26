<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Exception\ImportException;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Trait\SingletonsTrait;

class Validator
{
    use SingletonsTrait;

    /** @var AttributeModel[] $existingAttributes */
    private array $existingAttributes = [];
    private array $requiredAttributes = [];

    private Config $config;
    private Worker $worker;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function getWorker() : Worker
    {
        return $this->worker;
    }

    public function setWorker(Worker $worker) : void
    {
        $this->worker = $worker;
    }

    public function getRequiredAttributes() : array
    {
        return $this->requiredAttributes;
    }

    public function getExistingAttributes(): array
    {
        return $this->existingAttributes;
    }

    public function fetchStoredAttributes(): void
    {
        $worker = $this->getWorker();
        $container = $worker->getContainer();
        $domainKey = $container->getDomainKey();
        $setModel = $this->makeAttributeSetModel();
        foreach ($setModel->findAttributes($domainKey) as $attribute)
        {
            $this->existingAttributes[$attribute[_ATTR::NAME->column()]] = $attribute;
        }
    }

    public function analyseAttribute(string $name): void
    {
        $attributes = $this->getExistingAttributes();
        if (!key_exists($name, $attributes) && $name !== _ENTITY::ID->column())
        {
            $this->requiredAttributes[] = $name;
        }
    }

    public function analyse(array $columns): void
    {
        foreach ($columns as $column)
        {
            $this->analyseAttribute($column);
        }
    }

    /**
     * @throws ImportException
     */
    public function validate() : void
    {
        $worker = $this->getWorker();
        $container = $worker->getContainer();
        $driver = $container->getDriver();
        $columns = $driver->getHeader();
        $this->fetchStoredAttributes();
        $this->analyse($columns);
        $this->validateAttributes();
    }

    /**
     * @throws ImportException
     */
    public function validateAttributes() : bool
    {
        $config = $this->getConfig();
        $attributes = [];
        foreach($this->getRequiredAttributes() as $name)
        {
            if(!$config->hasAttribute($name))
            {
                $attributes[] = $name;
            }
        }
        if(count($attributes) > 0)
        {
            ImportException::configMissedAttributes($attributes);
        }
        return true;
    }

}