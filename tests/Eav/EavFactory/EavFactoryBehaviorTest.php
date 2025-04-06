<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Kuperwood\Eav\Factory\EavFactory;
use Tests\TestCase;

class EavFactoryBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Factory\EavFactory::createDomain
     */
    public function entity_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domainKey = $this->eavFactory->createDomain();
        $factory->createEntity($domainKey);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Factory\EavFactory::createEntity
     */
    public function entity_attr_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $factory->createEntity($domainKey, $setKey);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attribute_set_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domainKey = $this->eavFactory->createDomain();
        $factory->createAttributeSet($domainKey);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Factory\EavFactory::createGroup
     */
    public function attribute_group_attribute_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $setKey = $this->eavFactory->createAttributeSet();
        $factory->createGroup($setKey);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttribute
     */
    public function attribute_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domainKey = $this->eavFactory->createDomain();
        $factory->createAttribute($domainKey);
    }
}