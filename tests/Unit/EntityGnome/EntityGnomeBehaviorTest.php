<?php

declare(strict_types=1);

namespace Tests\Unit\EntityGnome;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityBag;
use Kuperwood\Eav\EntityGnome;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Value\ValueManager;
use Kuperwood\Eav\Value\ValueValidator;
use Tests\TestCase;

class EntityGnomeBehaviorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $entity = new Entity();
        $this->gnome = new EntityGnome($entity);
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::find
     */
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
            ->onlyMethods(['setAttributeSet', 'setDomainKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('setDomainKey')
            ->with($domainModel->getKey());
        $entity->expects($this->once())
            ->method('setAttributeSet')
            ->with($set);
        $entity->setKey($entityModel->getKey());
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->onlyMethods(['makeAttributeSet'])
            ->setConstructorArgs([$entity])
            ->getMock();
        $gnome->expects($this->once())
            ->method('makeAttributeSet')
            ->willReturn($set);

        $result = $gnome->find();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::save
     */
    public function save_fetch_containers() {
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $set->expects($this->once())->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['beforeSave'])
            ->getMock();
        $gnome->expects($this->once())->method('beforeSave');
        $gnome->save();
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::save
     */
    public function save_values() {
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['clear'])
            ->getMock();
        $bag->expects($this->once())
            ->method('clear');
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('phone', 'email');
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['save'])
            ->getMock();
        $strategy->expects($this->exactly(2))
            ->method('save');
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getAttribute', 'getStrategy'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturn($attribute);
        $container->expects($this->exactly(2))
            ->method('getStrategy')
            ->willReturn($strategy);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $attrSet->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet', 'getBag'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $entity->expects($this->once())
            ->method('getBag')
            ->willReturn($bag);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['beforeSave'])
            ->getMock();
        $gnome->expects($this->once())->method('beforeSave');
        $gnome->save();
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::save
     */
    public function save_result_created() {
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['beforeSave'])
            ->getMock();
        $gnome->expects($this->once())
            ->method('beforeSave')
            ->willReturn(1);
        $result = $gnome->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::save
     */
    public function save_result_updated() {
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['beforeSave'])
            ->getMock();
        $gnome->expects($this->once())
            ->method('beforeSave')
            ->willReturn(2);
        $result = $gnome->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::validate
     */
    public function validate_with_errors() {
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('email', 'phone');
        $validator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['validateField'])
            ->getMock();
        $emailErrors = ['value' => 'invalid'];
        $phoneErrors = ['value' => 'not valid'];
        $validator->expects($this->exactly(2))
            ->method('validateField')
            ->willReturn($emailErrors, $phoneErrors);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueValidator', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getValueValidator')
            ->willReturn($validator);
        $container->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturn($attribute);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('fetchContainers');
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $gnome = new EntityGnome($entity);
        $result = $gnome->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $result->getMessage());
        $this->assertEquals(['email' => $emailErrors, 'phone' => $phoneErrors], $result->getData());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::validate
     */
    public function validate_passed() {
        $validator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['validateField'])
            ->getMock();
        $emailErrors = null;
        $phoneErrors = null;
        $validator->expects($this->exactly(2))
            ->method('validateField')
            ->willReturn($emailErrors, $phoneErrors);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueValidator', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getValueValidator')
            ->willReturn($validator);
        $container->expects($this->never())
            ->method('getAttribute');
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('fetchContainers');
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $gnome = new EntityGnome($entity);
        $result = $gnome->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $result->getMessage());
        $this->assertNull($result->getData());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::delete
     */
    public function delete() {
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('email', 'phone');
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['delete'])
            ->getMock();
        $strategyResult = (new Result())->deleted();
        $strategy->expects($this->exactly(2))
            ->method('delete')
            ->willReturn($strategyResult, $strategyResult);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getStrategy', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getStrategy')
            ->willReturn($strategy);
        $container->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturn($attribute);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('fetchContainers');
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $record = $this->getMockBuilder(EntityModel::class)
            ->onlyMethods(['findAndDelete'])
            ->getMock();
        $record->expects($this->once())
            ->method('findAndDelete')
            ->willReturn(true);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet', 'getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['makeEntityModel'])
            ->getMock();
        $gnome->expects($this->once())
            ->method('makeEntityModel')
            ->willReturn($record);
        $result = $gnome->delete();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $result->getMessage());
        $this->assertSame([
            "email" => $strategyResult,
            "phone" => $strategyResult
        ], $result->getData());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::delete
     */
    public function not_deleted() {
        $record = $this->getMockBuilder(EntityModel::class)
            ->onlyMethods(['findAndDelete'])
            ->getMock();
        $record->expects($this->once())
            ->method('findAndDelete')
            ->willReturn(false);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet', 'getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->method('getContainers')->willReturn([]);
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['makeEntityModel'])
            ->getMock();
        $gnome->expects($this->once())
            ->method('makeEntityModel')
            ->willReturn($record);
        $result = $gnome->delete();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_DELETED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers EntityGnome::toArray
     */
    public function to_array() {
        $data = [
            'email' => 'email@emal.com',
            'phone' => '1234567',
        ];
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('email', 'phone');
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['getValue'])
            ->getMock();
        $valueManager->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn($data['email'], $data['phone']);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueManager', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getValueManager')
            ->willReturn($valueManager);
        $container->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturn($attribute);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $result = $entity->toArray();
        $this->assertEquals($data, $result);
    }
}