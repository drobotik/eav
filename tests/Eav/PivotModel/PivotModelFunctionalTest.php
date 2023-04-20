<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotModel;

use Drobotik\Eav\Model\PivotModel;
use PHPUnit\Framework\TestCase;

class PivotModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::setDomainKey, \Drobotik\Eav\Model\PivotModel::getDomainKey
     */
    public function domain_key() {
        $model = new PivotModel();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::setAttrSetKey, \Drobotik\Eav\Model\PivotModel::getAttrSetKey
     */
    public function attr_set_key() {
        $model = new PivotModel();
        $model->setAttrSetKey(123);
        $this->assertEquals(123, $model->getAttrSetKey());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::setGroupKey, \Drobotik\Eav\Model\PivotModel::getGroupKey
     */
    public function group_key() {
        $model = new PivotModel();
        $model->setGroupKey(123);
        $this->assertEquals(123, $model->getGroupKey());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::setAttrKey, \Drobotik\Eav\Model\PivotModel::getAttrKey
     */
    public function attr_key() {
        $model = new PivotModel();
        $model->setAttrKey(123);
        $this->assertEquals(123, $model->getAttrKey());
    }
}