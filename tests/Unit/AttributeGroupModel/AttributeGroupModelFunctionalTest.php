<?php

declare(strict_types=1);

namespace Tests\Unit\AttributeGroupModel;

use Drobotik\Eav\Model\AttributeGroupModel;
use PHPUnit\Framework\TestCase;

class AttributeGroupModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers AttributeGroupModel::setAttrSetKey, AttributeGroupModel::getAttrSetKey,
     */
    public function attr_set_key() {
        $model = new AttributeGroupModel();
        $model->setAttrSetKey(123);
        $this->assertEquals(123, $model->getAttrSetKey());
    }

    /**
     * @test
     * @group functional
     * @covers AttributeGroupModel::setName, AttributeGroupModel::getName,
     */
    public function name_accessor() {
        $model = new AttributeGroupModel();
        $model->setName('test');
        $this->assertEquals('test', $model->getName());
    }
}