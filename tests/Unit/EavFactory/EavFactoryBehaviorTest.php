<?php

namespace Tests\Unit\EavFactory;

use DateTime;
use Kuperwood\Dev\EavFactory;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;
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