<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import;

use Drobotik\Eav\Import\Attributes\Worker as AttributesWorker;
use Drobotik\Eav\Import\Content\Worker as ContentWorker;
use Drobotik\Eav\Driver;

class ImportContainer
{
    private int           $domainKey;
    private int           $setKey;
    private Driver        $driver;
    private ImportManager $manager;
    private AttributesWorker    $attributesWorker;
    private ContentWorker          $contentWorker;

    public function getDomainKey() : int
    {
        return $this->domainKey;
    }

    public function setDomainKey(int $key) : void
    {
        $this->domainKey = $key;
    }

    public function getSetKey() : int
    {
        return $this->setKey;
    }

    public function setSetKey(int $key) : void
    {
        $this->setKey = $key;
    }

    public function setManager(ImportManager $manager): void
    {
        $manager->setContainer($this);
        $this->manager = $manager;
    }

    public function getManager(): ImportManager
    {
        return $this->manager;
    }

    public function getAttributesWorker() : AttributesWorker
    {
        return $this->attributesWorker;
    }

    public function setAttributesWorker(AttributesWorker $worker) : void
    {
        $worker->setContainer($this);
        $this->attributesWorker = $worker;
    }

    public function getContentWorker() : ContentWorker
    {
        return $this->contentWorker;
    }

    public function setContentWorker(ContentWorker $worker) : void
    {
        $worker->setContainer($this);
        $this->contentWorker = $worker;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }
}