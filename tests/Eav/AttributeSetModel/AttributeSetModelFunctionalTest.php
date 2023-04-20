<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetModel;

use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Trait\SingletonsTrait;
use Tests\TestCase;
use Throwable;

class AttributeSetModelFunctionalTest extends TestCase
{
    use SingletonsTrait;
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::getName, \Drobotik\Eav\Model\AttributeSetModel::setName
     */
    public function name_accessor() {
        $model = new AttributeSetModel();
        $model->setName("test");
        $this->assertEquals("test", $model->getName());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::getDomainKey, \Drobotik\Eav\Model\AttributeSetModel::setDomainKey
     */
    public function domainKey() {
        $model = new AttributeSetModel();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::firstOrFail
     */
    public function first_or_fail() {
        $record = $this->eavFactory->createAttributeSet();
        $model = $this->makeAttributeSetModel();
        $result = $model->firstOrFail($record->getKey());
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals($record->toArray(), $result->toArray());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::firstOrFail
     */
    public function first_or_fail_exception() {
        $this->expectException(Throwable::class);
        $model = $this->makeAttributeSetModel();
        $model->firstOrFail(123);
    }
}