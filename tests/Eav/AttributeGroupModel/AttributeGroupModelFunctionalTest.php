<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeGroupModel;

use Drobotik\Eav\Model\AttributeGroupModel;
use PHPUnit\Framework\TestCase;

class AttributeGroupModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeGroupModel::setAttrSetKey, \Drobotik\Eav\Model\AttributeGroupModel::getAttrSetKey,
     */
    public function attr_set_key() {
        $model = new AttributeGroupModel();
        $model->setAttrSetKey(123);
        $this->assertEquals(123, $model->getAttrSetKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeGroupModel::setName, \Drobotik\Eav\Model\AttributeGroupModel::getName,
     */
    public function name_accessor() {
        $model = new AttributeGroupModel();
        $model->setName('test');
        $this->assertEquals('test', $model->getName());
    }
}