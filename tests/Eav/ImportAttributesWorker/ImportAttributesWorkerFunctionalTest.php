<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesWorker;

use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\Validator;
use Drobotik\Eav\Import\Attributes\Worker;
use PHPUnit\Framework\TestCase;

class ImportAttributesWorkerFunctionalTest extends TestCase
{
    private Worker $worker;

    public function setUp(): void
    {
        parent::setUp();
        $this->worker = new Worker();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::setConfig
     * @covers \Drobotik\Eav\Import\Attributes\Worker::getConfig
     * @covers \Drobotik\Eav\Import\Attributes\Worker::isConfig
     */
    public function config()
    {
        $config = new Config();
        $this->assertFalse($this->worker->isConfig());
        $this->worker->setConfig($config);
        $this->assertSame($config, $this->worker->getConfig());
        $this->assertTrue($this->worker->isConfig());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::getValidator
     */
    public function validator()
    {
        $validator = $this->worker->getValidator();
        $this->assertInstanceOf(Validator::class, $validator);
        $newValidator = $this->worker->getValidator();
        $this->assertNotSame($validator, $newValidator);
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::createAttributes
     */
    public function create_attributes()
    {
        $attribute = new ConfigAttribute();
        $attributes = [$attribute];
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['getAttributes'])->getMock();
        $config->expects($this->once())->method('getAttributes')
            ->willReturn($attributes);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getConfig', 'createAttribute'])
            ->getMock();

        $worker->expects($this->once())->method('getConfig')
            ->willReturn($config);
        $worker->expects($this->once())->method('createAttribute')
            ->with($attribute);

        $worker->createAttributes();
    }
}