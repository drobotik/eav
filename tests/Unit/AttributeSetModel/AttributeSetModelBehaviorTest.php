<?php

declare(strict_types=1);

namespace Tests\Unit\AttributeSetModel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Drobotik\Eav\Model\AttributeSetModel;
use PHPUnit\Framework\TestCase;

class AttributeSetModelBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers AttributeSetModel::findAttributes
     */
    public function find_attributes() {
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
        $result = $model->findAttributes(1);
        $this->assertSame($collection, $result);
    }
}