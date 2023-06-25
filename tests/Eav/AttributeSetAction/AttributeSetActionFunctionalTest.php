<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetAction;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Strategy;
use Tests\TestCase;

class AttributeSetActionFunctionalTest extends TestCase
{
    private AttributeContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
        $this->container->makeAttributeSetAction();
        $this->action = $this->container->getAttributeSetAction();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialize_attribute() {
        $attribute = [
            _ATTR::ID->column() => 1,
            _ATTR::DOMAIN_ID->column() => 1,
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];
        $result = $this->action->initializeAttribute($attribute);
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute,  $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialized_attribute_with_pivot() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attributeKey = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attributeKey);

        $qb = Connection::get()->createQueryBuilder();
        $attributeRecord = $qb->select('*')->from(_ATTR::table())
            ->executeQuery()->fetchAssociative();

        $result = $this->action->initializeAttribute($attributeRecord);
        $this->assertEquals($attributeRecord, $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initializeStrategy
     */
    public function initialized_strategy() {
        $attribute = new Attribute();
        $result = $this->action->initializeStrategy($attribute);
        $this->assertInstanceOf(Strategy::class, $result);
        $this->assertSame($result, $this->container->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initialize
     */
    public function initialize() {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attKey = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attKey);
        $valueModel = $this->eavFactory->createValue(
            ATTR_TYPE::STRING, $domainKey, $entityKey, $attKey, "test");

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $this->container->setAttributeSet($attrSet);

        $qb = Connection::get()->createQueryBuilder();
        $attributeRecord = $qb->select('*')->from(_ATTR::table())
            ->executeQuery()->fetchAssociative();
        $this->action->initialize($attributeRecord);

        // attribute
        $attribute = $this->container->getAttribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attributeRecord, $attribute->getBag()->getFields());
        // value
        $valueManager = $this->container->getValueManager();
        $this->assertEquals($valueModel->getKey(), $valueManager->getKey());
        $this->assertEquals($valueModel->getValue(), $valueManager->getStored());
    }
}