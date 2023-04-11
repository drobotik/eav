<?php

declare(strict_types=1);

namespace Tests\Unit\AttributeSetModel;

use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Trait\SingletonsTrait;
use Tests\TestCase;
use Throwable;

class AttributeSetModelFunctionalTest extends TestCase
{
    use SingletonsTrait;
    /**
     * @test
     * @group functional
     * @covers AttributeSetModel::getName, AttributeSetModel::setName
     */
    public function name_accessor() {
        $model = new AttributeSetModel();
        $model->setName("test");
        $this->assertEquals("test", $model->getName());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSetModel::getDomainKey, AttributeSetModel::setDomainKey
     */
    public function domainKey() {
        $model = new AttributeSetModel();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSetModel::firstOrFail
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
     * @covers AttributeSetModel::firstOrFail
     */
    public function first_or_fail_exception() {
        $this->expectException(Throwable::class);
        $model = $this->makeAttributeSetModel();
        $model->firstOrFail(123);
    }
}