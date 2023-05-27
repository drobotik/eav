<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Content;

use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Trait\RepositoryTrait;

class AttributeSet
{
    use RepositoryTrait;

    /** @var AttributeModel[]  */
    private array $attributes = [];
    private Worker $worker;

    public function setWorker(Worker $worker) : void
    {
        $this->worker = $worker;
    }

    public function getWorker() : Worker
    {
        return $this->worker;
    }


    public function appendAttribute(AttributeModel $attributeModel): void
    {
        $this->attributes[$attributeModel->getName()] = $attributeModel;
    }

    public function getAttribute(string $name) : AttributeModel
    {
        return $this->attributes[$name];
    }

    public function hasAttribute(string $name) : bool
    {
        return key_exists($name, $this->attributes);
    }

    public function initialize() : void
    {
        $worker = $this->getWorker();
        $container = $worker->getContainer();
        $driver = $container->getDriver();
        $columns = $driver->getHeader();
        $repository = $this->makeAttributeRepository();
        $attributes = $repository->getLinked($container->getDomainKey(), $container->getSetKey());
        foreach ($attributes as $attribute)
        {
            $name = $attribute->getName();
            if(in_array($name, $columns))
            {
                $this->appendAttribute($attribute);
            }
        }
    }
}