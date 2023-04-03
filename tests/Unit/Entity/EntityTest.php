<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityAction;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Result\Result;

use Tests\TestCase;

class EntityTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }

    /** @test */
    public function key() {
        $this->assertNull($this->entity->getKey());
        $this->entity->setKey(1);
        $this->assertEquals(1, $this->entity->getKey());
        $this->entity->setKey(null);
        $this->assertNull($this->entity->getKey());
    }

    /** @test */
    public function domain_key() {
        $this->assertNull($this->entity->getDomainKey());
        $this->entity->setDomainKey(1);
        $this->assertEquals(1, $this->entity->getDomainKey());
        $this->entity->setDomainKey(null);
        $this->assertNull($this->entity->getDomainKey());
    }

    /** @test */
    public function create() {
        $data = [
            "phone" => "1234567890",
            "email" => "test@email.com"
        ];
        $entityAction = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['saveValue'])
            ->getMock();
        $entityAction->expects($this->exactly(2))
            ->method('saveValue')
            ->with($this->callback(fn($arg) => in_array($arg, array_values($data))));
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getEntityAction'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getEntityAction')
            ->willReturn($entityAction);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainer'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $attrSet->expects($this->exactly(2))
            ->method('getContainer')
            ->with($this->callback(fn($arg) => key_exists($arg, $data)))
            ->willReturn($container);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $result = $entity->create($data);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function find_no_key() {
        $result = $this->entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_no_record() {
        $this->entity->setKey(123);
        $result = $this->entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function find() {
        $domainModel = $this->eavFactory->createDomain();
        $setModel = $this->eavFactory->createAttributeSet();
        $entityModel = $this->eavFactory->createEntity($domainModel, $setModel);

        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'setKey', 'setEntity'])
            ->getMock();
        $set->expects($this->once())->method('setKey')->with($setModel->getKey());
        $set->expects($this->once())->method('fetchContainers');
        $set->expects($this->once())->method('setEntity');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['makeAttributeSet', 'setAttributeSet', 'setDomainKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('setDomainKey')
            ->with($domainModel->getKey());
        $entity->expects($this->once())
            ->method('makeAttributeSet')
            ->willReturn($set);
        $entity->expects($this->once())
            ->method('setAttributeSet')
            ->with($set);

        $entity->setKey($entityModel->getKey());

        $result = $entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }

}