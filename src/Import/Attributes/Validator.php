<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

use Drobotik\Eav\Exception\ImportException;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Trait\ImportContainerTrait;
use Drobotik\Eav\Trait\RepositoryTrait;

class Validator
{
    use ImportContainerTrait;
    use RepositoryTrait;
    /** @var AttributeModel[] $existingAttributes */
    private array $existingAttributes;
    private array $requiredAttributes = [];

    private Config $config;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig() : Config
    {
        return $this->config;
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
        $container = $this->getContainer();
        $domainKey = $container->getDomainKey();
        $repository = $this->makeAttributeRepository();
        /** @var AttributeModel $attribute */
        foreach ($repository->getStored($domainKey) as $attribute)
        {
            $this->existingAttributes[$attribute->getName()] = $attribute;
        }
    }

    public function analyseAttribute(string $name): void
    {
        $attributes = $this->getExistingAttributes();
        if (!key_exists($name, $attributes))
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
        $container = $this->getContainer();
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