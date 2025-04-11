<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSet;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Tests\TestCase;

class AttributeSetFunctionalTest extends TestCase
{
    protected AttributeSet $instance;

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new AttributeSet();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::setKey
     * @covers \Kuperwood\Eav\AttributeSet::getKey
     * @covers \Kuperwood\Eav\AttributeSet::hasKey
     */
    public function key() {
        $this->assertFalse($this->instance->hasKey());
        $this->instance->setKey(1);
        $this->assertEquals(1, $this->instance->getKey());
        $this->assertTrue($this->instance->hasKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::setKey
     * @covers \Kuperwood\Eav\AttributeSet::getKey
     * @covers \Kuperwood\Eav\AttributeSet::hasKey
     */
    public function key_zero() {
        $this->instance->setKey(0);
        $this->assertEquals(0, $this->instance->getKey());
        $this->assertFalse($this->instance->hasKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::setEntity
     * @covers \Kuperwood\Eav\AttributeSet::getEntity
     */
    public function entity()
    {
        $entity = new Entity();
        $this->instance->setEntity($entity);
        $this->assertSame($entity, $this->instance->getEntity());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::setName
     * @covers \Kuperwood\Eav\AttributeSet::getName
     */
    public function name_accessor() {
        $this->instance->setName('test');
        $this->assertEquals('test', $this->instance->getName());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::getContainers
     */
    public function get_containers() {
        $this->assertEquals([], $this->instance->getContainers());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::pushContainer
     */
    public function push_container() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertEquals([$attribute->getName() => $container], $this->instance->getContainers());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::getContainer
     */
    public function get_container() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertSame($container, $this->instance->getContainer($attribute->getName()));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::getContainer
     */
    public function get_container_return_null() {
        $this->assertNull($this->instance->getContainer('test'));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::hasContainer
     */
    public function has_container() {
        $this->assertFalse($this->instance->hasContainer('test'));
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertTrue($this->instance->hasContainer('test'));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::hasContainers
     */
    public function has_containers() {
        $this->assertFalse($this->instance->hasContainers());
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertTrue($this->instance->hasContainers());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSet::resetContainers
     */
    public function reset_containers() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->instance->resetContainers();
        $this->assertFalse($this->instance->hasContainers());
    }
}