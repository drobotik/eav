<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Trait\SingletonsTrait;
use Tests\TestCase;

class AttributeSetModelTest extends TestCase
{
    use SingletonsTrait;
    /** @test */
    public function first_or_fail() {
        $record = $this->eavFactory->createAttributeSet();
        $model = $this->makeAttributeSetModel();
        $result = $model->firstOrFail($record->getKey());
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals($record->toArray(), $result->toArray());
    }

    /** @test */
    public function first_or_fail_exception() {
        $this->expectException(\Throwable::class);
        $model = $this->makeAttributeSetModel();
        $model->firstOrFail(123);
    }

}