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
use Drobotik\Eav\Trait\SingletonsTrait;

class AttributeSet
{
    use SingletonsTrait;

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


    public function appendAttribute(array $attribute): void
    {
        $this->attributes[$attribute[_ATTR::NAME->column()]] = $attribute;
    }

    public function getAttribute(string $name) : array
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
        $attributeModel = $this->makeAttributeSetModel();
        $attributes = $attributeModel->findAttributes($container->getDomainKey(), $container->getSetKey());
        foreach ($attributes as $attribute)
        {
            $name = $attribute[_ATTR::NAME->column()];
            if(in_array($name, $columns))
            {
                $this->appendAttribute($attribute);
            }
        }
    }
}