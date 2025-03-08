<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigValidator;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\ImportException;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\Validator;
use Drobotik\Eav\Import\Attributes\Worker;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;

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
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getWorker
     * @covers \Drobotik\Eav\Import\Attributes\Validator::setWorker
     */
    public function worker()
    {
        $worker = new Worker();
        $this->validator->setWorker($worker);
        $this->assertSame($worker, $this->validator->getWorker());
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

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getContainer'])->getMock();
        $worker->expects($this->once())->method('getContainer')->willReturn($container);

        $attr1 = [_ATTR::NAME => 'test1'];
        $attr2 = [_ATTR::NAME => 'test2'];
        $collection = [$attr1, $attr2];

        $setModel = $this->getMockBuilder(AttributeSetModel::class)
            ->onlyMethods(['findAttributes'])->getMock();
        $setModel->expects($this->once())->method('findAttributes')
            ->with(123)
            ->willReturn($collection);

        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['makeAttributeSetModel', 'getWorker'])
            ->getMock();
        $analyzer->expects($this->once())->method('getWorker')->willReturn($worker);
        $analyzer->expects($this->once())->method('makeAttributeSetModel')->willReturn($setModel);

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
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::analyseAttribute
     * @covers \Drobotik\Eav\Import\Attributes\Validator::getRequiredAttributes
     */
    public function analyse_entity_key()
    {
        $attributes = [_ENTITY::ID => null];
        $analyzer = $this->getMockBuilder(Validator::class)
            ->onlyMethods(['getExistingAttributes'])
            ->getMock();
        $analyzer->expects($this->once())->method('getExistingAttributes')->willReturn($attributes);
        $analyzer->analyseAttribute(_ENTITY::ID);
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

        $attribute->setFields([_ATTR::NAME => 'one', _ATTR::TYPE => ATTR_TYPE::TEXT]);
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
        $attribute->setFields([_ATTR::NAME => 'one', _ATTR::TYPE => ATTR_TYPE::TEXT]);
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

}