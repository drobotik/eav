<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityModel;

use Drobotik\Eav\Model\EntityModel;
use Tests\TestCase;

class EntityModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\EntityModel::setAttrSetKey, \Drobotik\Eav\Model\EntityModel::getAttrSetKey
     */
    public function domain_key() {
        $model = new EntityModel();
        $model->setDomainKey(456);
        $this->assertEquals(456, $model->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\EntityModel::setAttrSetKey, \Drobotik\Eav\Model\EntityModel::getAttrSetKey
     */
    public function attr_set_key() {
        $model = new EntityModel();
        $model->setAttrSetKey(456);
        $this->assertEquals(456, $model->getAttrSetKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\EntityModel::findAndDelete
     */
    public function find_and_delete() {
        $model = new EntityModel();
        $result = $model->findAndDelete(1);
        $this->assertFalse($result);
        $this->eavFactory->createEntity();
        $result = $model->findAndDelete(1);
        $this->assertTrue($result);
    }
}