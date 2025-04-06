<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Traits;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Factory\EavFactory;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Traits\SingletonsTrait;
use Kuperwood\Eav\Value\ValueParser;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class SingletonsTraitFunctionalTest extends TestCase
{
    use SingletonsTrait;

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeAttributeSetModel
     */
    public function testMakeAttributeSetModel()
    {
        $attributeSetModel = $this->makeAttributeSetModel();
        $this->assertInstanceOf(AttributeSetModel::class, $attributeSetModel);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeAttributeContainer
     */
    public function testMakeAttributeContainer()
    {
        $attributeContainer = $this->makeAttributeContainer();
        $this->assertInstanceOf(AttributeContainer::class, $attributeContainer);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeEntityModel
     */
    public function testMakeEntityModel()
    {
        $entityModel = $this->makeEntityModel();
        $this->assertInstanceOf(EntityModel::class, $entityModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeAttributeSet
     */
    public function testMakeAttributeSet()
    {
        $attributeSet = $this->makeAttributeSet();
        $this->assertInstanceOf(AttributeSet::class, $attributeSet);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeDomainModel
     */
    public function testMakeDomainModel()
    {
        $domainModel = $this->makeDomainModel();
        $this->assertInstanceOf(DomainModel::class, $domainModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeGroupModel
     */
    public function testMakeGroupModel()
    {
        $groupModel = $this->makeGroupModel();
        $this->assertInstanceOf(AttributeGroupModel::class, $groupModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makePivotModel
     */
    public function testMakePivotModel()
    {
        $pivotModel = $this->makePivotModel();
        $this->assertInstanceOf(PivotModel::class, $pivotModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeValueModel
     */
    public function testMakeValueModel()
    {
        $valueModel = $this->makeValueModel();
        $this->assertInstanceOf(ValueBase::class, $valueModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeAttributeModel
     */
    public function testMakeAttributeModel()
    {
        $attributeModel = $this->makeAttributeModel();
        $this->assertInstanceOf(AttributeModel::class, $attributeModel);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeEavFactory
     */
    public function testMakeEavFactory()
    {
        $eavFactory = $this->makeEavFactory();
        $this->assertInstanceOf(EavFactory::class, $eavFactory);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeValueParser
     */
    public function testMakeValueParser()
    {
        $valueParser = $this->makeValueParser();
        $this->assertInstanceOf(ValueParser::class, $valueParser);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\SingletonsTrait::makeFakerGenerator
     */
    public function testMakeFakerGenerator()
    {
        $fakerGenerator = $this->makeFakerGenerator();
        $this->assertInstanceOf(Generator::class, $fakerGenerator);
    }
}