<?php

declare(strict_types=1);

namespace Tests\Unit\EntityModel;

use Kuperwood\Eav\Model\EntityModel;
use Tests\TestCase;

class EntityModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers EntityModel::setAttrSetKey, EntityModel::getAttrSetKey
     */
    public function domain_key() {
        $model = new EntityModel();
        $model->setDomainKey(456);
        $this->assertEquals(456, $model->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers EntityModel::setAttrSetKey, EntityModel::getAttrSetKey
     */
    public function attr_set_key() {
        $model = new EntityModel();
        $model->setAttrSetKey(456);
        $this->assertEquals(456, $model->getAttrSetKey());
    }
    /**
     * @test
     * @group functional
     * @covers EntityModel::findAndDelete
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