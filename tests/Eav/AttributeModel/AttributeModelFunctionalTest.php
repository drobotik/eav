<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeModel;

use Drobotik\Eav\Model\AttributeModel;
use PHPUnit\Framework\TestCase;

class AttributeModelFunctionalTest extends TestCase
{
    private AttributeModel $model;
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new AttributeModel();
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setName, AttributeModel::getName
     */
    public function name_accessor() {
        $this->model->setName('test');
        $this->assertEquals('test', $this->model->getName());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setDomainKey, AttributeModel::getDomainKey
     */
    public function domain_key() {
        $this->model->setDomainKey(123);
        $this->assertEquals(123, $this->model->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setType, AttributeModel::getType
     */
    public function type() {
        $this->model->setType('test');
        $this->assertEquals('test', $this->model->getType());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setDescription, AttributeModel::getDescription
     */
    public function description() {
        $this->model->setDescription('test');
        $this->assertEquals('test', $this->model->getDescription());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setDefaultValue, AttributeModel::getDefaultValue
     */
    public function default_value() {
        $this->model->setDefaultValue('test');
        $this->assertEquals('test', $this->model->getDefaultValue());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setSource, AttributeModel::getSource
     */
    public function source() {
        $this->model->setSource('test');
        $this->assertEquals('test', $this->model->getSource());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeModel::setStrategy, AttributeModel::getStrategy
     */
    public function strategy() {
        $this->model->setStrategy('test');
        $this->assertEquals('test', $this->model->getStrategy());
    }


}