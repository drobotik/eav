<?php

namespace Tests\Unit\Attribute;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /** @test */
    public function get_attributes() {
        $collection = new Collection(123);
        $belongsToMany = $this->getMockBuilder(BelongsToMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();
        $belongsToMany->expects($this->once())
            ->method('get')
            ->willReturn($collection);
        $record = $this->getMockBuilder(AttributeSetModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['attributes'])
            ->getMock();
        $record->expects($this->once())
            ->method('attributes')
            ->willReturn($belongsToMany);
        $model = $this->getMockBuilder(AttributeSetModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['firstOrFail'])
            ->getMock();
        $model->expects($this->once())
            ->method('firstOrFail')
            ->willReturn($record);
        $result = $model->getAttrs(1);
        $this->assertSame($collection, $result);
    }

}