<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigAnalyser;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\ImportException;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\Validator;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Repository\AttributeRepository;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class ValidatorFunctionalTest extends TestCase
{
    private Validator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getConfig
     * @covers \Drobotik\Eav\Import\Attributes\Validator::setConfig
     */
    public function config()
    {
        $config = new Config();
        $this->validator->setConfig($config);
        $this->assertSame($config, $this->validator->getConfig());
    }

    /**
     * @test
     *
     * @group functional
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getExistingAttributes
     * @covers \Drobotik\Eav\Import\Attributes\Validator::fetchStoredAttributes
     */
    public function attributes()
    {
        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDomainKey'])->getMock();
        $container->expects($this->once())->method('getDomainKey')->willReturn(123);

        $attr1 = new AttributeModel();
        $attr1->setName('test1');
        $attr2 = new AttributeModel();
        $attr2->setName('test2');
        $data = [$attr1, $attr2];
        $collection = new Collection($data);
        $repository = $this->getMockBuilder(AttributeRepository::class)
            ->onlyMethods(['getStored'])->getMock();
        $repository->expects($this->once())->method('getStored')
            ->with(123)
            ->willReturn($collection);

        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['makeAttributeRepository', 'getContainer'])
            ->getMock();
        $analyzer->expects($this->once())->method('getContainer')->willReturn($container);
        $analyzer->expects($this->once())->method('makeAttributeRepository')->willReturn($repository);

        $analyzer->fetchStoredAttributes();
        $this->assertEquals([
            'test1' => $attr1 ,
            'test2' => $attr2,
        ], $analyzer->getExistingAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::analyseAttribute
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getRequiredAttributes
     */
    public function analyse_missed_attribute()
    {
        $attributes = ['test' => null];
        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['getExistingAttributes'])
            ->getMock();
        $analyzer->expects($this->exactly(2))->method('getExistingAttributes')->willReturn($attributes);

        $analyzer->analyseAttribute('name');
        $analyzer->analyseAttribute('price');
        $this->assertEquals(['name', 'price'], $analyzer->getRequiredAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::analyseAttribute
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getRequiredAttributes
     */
    public function analyse_existing_attribute()
    {
        $attribute = new AttributeModel();

        $attributes = ['test' => $attribute];
        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['getExistingAttributes'])
            ->getMock();
        $analyzer->expects($this->exactly(1))->method('getExistingAttributes')->willReturn($attributes);
        $analyzer->analyseAttribute('test');
        $this->assertEquals([], $analyzer->getRequiredAttributes());
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::analyse
     */
    public function analyse()
    {
        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['analyseAttribute'])
            ->getMock();
        $analyzer->expects($this->once())->method('analyseAttribute')
            ->with('test');

        $analyzer->analyse(['test']);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::validateAttributes
     */
    public function validate_attributes()
    {
        $config = new Config();
        $attribute = new ConfigAttribute();

        $attribute->setFields([_ATTR::NAME->column() => 'one', _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()]);
        $config->appendAttribute($attribute);
        $validator = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['getRequiredAttributes', 'getConfig'])->getMock();
        $validator->expects($this->once())->method('getConfig')
            ->willReturn($config);
        $validator->expects($this->once())
            ->method('getRequiredAttributes')
            ->willReturn(['two' => 'two', 'three' => 'three']);
        $this->expectException(ImportException::class);
        $this->expectExceptionMessage(
            sprintf(ImportException::CONFIG_MISSED_ATTRIBUTES, 'two,three')
        );
        $validator->validateAttributes();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::validateAttributes
     */
    public function validate_attributes_success()
    {
        $config = new Config();
        $attribute = new ConfigAttribute();
        $attribute->setFields([_ATTR::NAME->column() => 'one', _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()]);
        $config->appendAttribute($attribute);
        $validator = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['getRequiredAttributes', 'getConfig'])->getMock();
        $validator->method('getConfig')->willReturn($config);
        $validator->method('getRequiredAttributes')->willReturn(['one' => 'one']);
        $this->assertTrue($validator->validateAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::validateAttributes
     */
    public function validate_attributes_empty()
    {
        $config = new Config();
        $this->validator->setConfig($config);
        $this->assertTrue($this->validator->validateAttributes());
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::validatePivots
     */
    public function validate()
    {
        $columns = [123];
        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['getHeader'])->getMock();
        $driver->expects($this->once())->method('getHeader')
            ->willReturn($columns);
        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDriver'])->getMock();
        $container->expects($this->once())->method('getDriver')->willReturn($driver);
        $validator = $this->getMockBuilder(Validator::class)
            ->onlyMethods([
                'getContainer',
                'fetchStoredAttributes',
                'analyse',
                'validateAttributes'
            ])->getMock();
        $validator->expects($this->once())->method('getContainer')->willReturn($container);
        $validator->expects($this->once())->method('fetchStoredAttributes');
        $validator->expects($this->once())->method('analyse')->with($columns);
        $validator->expects($this->once())->method('validateAttributes');
        $validator->validate();
    }
}