<?php

namespace Tests\Unit\EavFactory;

use DateTime;
use Drobotik\Dev\EavFactory;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Tests\TestCase;

class EavFactoryBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers EavFactory::createDomain
     */
    public function entity_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createEntity($domain);
    }
    /**
     * @test
     * @group behavior
     * @covers EavFactory::createEntity
     */
    public function entity_attr_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $factory->createEntity($domain, $attrSet);
    }
    /**
     * @test
     * @group behavior
     * @covers EavFactory::createAttributeSet
     */
    public function attribute_set_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttributeSet($domain);
    }
    /**
     * @test
     * @group behavior
     * @covers EavFactory::createGroup
     */
    public function attribute_group_attribute_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $set = $this->eavFactory->createAttributeSet();
        $factory->createGroup($set);
    }
    /**
     * @test
     * @group behavior
     * @covers EavFactory::createAttribute
     */
    public function attribute_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttribute($domain);
    }
}