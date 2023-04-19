<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSet;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use PHPUnit\Framework\TestCase;

class AttributeSetFunctionalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new AttributeSet();
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSet::setKey, AttributeSet::getKey, AttributeSet::hasKey
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
     * @covers AttributeSet::setKey, AttributeSet::getKey, AttributeSet::hasKey
     */
    public function key_zero() {
        $this->instance->setKey(0);
        $this->assertEquals(0, $this->instance->getKey());
        $this->assertFalse($this->instance->hasKey());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSet::setName, AttributeSet::getName
     */
    public function name_accessor() {
        $this->instance->setName('test');
        $this->assertEquals('test', $this->instance->getName());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSet::getContainers
     */
    public function get_containers() {
        $this->assertEquals([], $this->instance->getContainers());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSet::pushContainer
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
     * @covers AttributeSet::getContainer
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
     * @covers AttributeSet::getContainer
     */
    public function get_container_return_null() {
        $this->assertNull($this->instance->getContainer('test'));
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSet::hasContainer
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
     * @covers AttributeSet::hasContainers
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
}