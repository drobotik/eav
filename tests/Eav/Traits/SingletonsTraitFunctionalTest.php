<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Traits;

use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Traits\SingletonsTrait;
use Drobotik\Eav\Value\ValueParser;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class SingletonsTraitFunctionalTest extends TestCase
{
    use SingletonsTrait;

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeAttributeSetModel
     */
    public function testMakeAttributeSetModel()
    {
        $attributeSetModel = $this->makeAttributeSetModel();
        $this->assertInstanceOf(AttributeSetModel::class, $attributeSetModel);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeAttributeContainer
     */
    public function testMakeAttributeContainer()
    {
        $attributeContainer = $this->makeAttributeContainer();
        $this->assertInstanceOf(AttributeContainer::class, $attributeContainer);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeEntityModel
     */
    public function testMakeEntityModel()
    {
        $entityModel = $this->makeEntityModel();
        $this->assertInstanceOf(EntityModel::class, $entityModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeAttributeSet
     */
    public function testMakeAttributeSet()
    {
        $attributeSet = $this->makeAttributeSet();
        $this->assertInstanceOf(AttributeSet::class, $attributeSet);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeDomainModel
     */
    public function testMakeDomainModel()
    {
        $domainModel = $this->makeDomainModel();
        $this->assertInstanceOf(DomainModel::class, $domainModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeGroupModel
     */
    public function testMakeGroupModel()
    {
        $groupModel = $this->makeGroupModel();
        $this->assertInstanceOf(AttributeGroupModel::class, $groupModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makePivotModel
     */
    public function testMakePivotModel()
    {
        $pivotModel = $this->makePivotModel();
        $this->assertInstanceOf(PivotModel::class, $pivotModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeValueModel
     */
    public function testMakeValueModel()
    {
        $valueModel = $this->makeValueModel();
        $this->assertInstanceOf(ValueBase::class, $valueModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeAttributeModel
     */
    public function testMakeAttributeModel()
    {
        $attributeModel = $this->makeAttributeModel();
        $this->assertInstanceOf(AttributeModel::class, $attributeModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeEavFactory
     */
    public function testMakeEavFactory()
    {
        $eavFactory = $this->makeEavFactory();
        $this->assertInstanceOf(EavFactory::class, $eavFactory);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeValueParser
     */
    public function testMakeValueParser()
    {
        $valueParser = $this->makeValueParser();
        $this->assertInstanceOf(ValueParser::class, $valueParser);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\SingletonsTrait::makeFakerGenerator
     */
    public function testMakeFakerGenerator()
    {
        $fakerGenerator = $this->makeFakerGenerator();
        $this->assertInstanceOf(Generator::class, $fakerGenerator);
    }
}