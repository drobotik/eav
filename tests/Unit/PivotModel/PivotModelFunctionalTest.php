<?php

declare(strict_types=1);

namespace Tests\Unit\PivotModel;

use Drobotik\Eav\Model\PivotModel;
use PHPUnit\Framework\TestCase;

class PivotModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers PivotModel::setDomainKey, PivotModel::getDomainKey
     */
    public function domain_key() {
        $model = new PivotModel();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }

    /**
     * @test
     * @group functional
     * @covers PivotModel::setAttrSetKey, PivotModel::getAttrSetKey
     */
    public function attr_set_key() {
        $model = new PivotModel();
        $model->setAttrSetKey(123);
        $this->assertEquals(123, $model->getAttrSetKey());
    }

    /**
     * @test
     * @group functional
     * @covers PivotModel::setGroupKey, PivotModel::getGroupKey
     */
    public function group_key() {
        $model = new PivotModel();
        $model->setGroupKey(123);
        $this->assertEquals(123, $model->getGroupKey());
    }

    /**
     * @test
     * @group functional
     * @covers PivotModel::setAttrKey, PivotModel::getAttrKey
     */
    public function attr_key() {
        $model = new PivotModel();
        $model->setAttrKey(123);
        $this->assertEquals(123, $model->getAttrKey());
    }
}