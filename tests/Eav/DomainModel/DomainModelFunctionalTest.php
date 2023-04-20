<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DomainModel;

use Drobotik\Eav\Model\DomainModel;
use PHPUnit\Framework\TestCase;

class DomainModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::setName
     * @covers \Drobotik\Eav\Model\DomainModel::getName
     */
    public function name_accessor() {
        $model = new DomainModel();
        $model->setName('test');
        $this->assertEquals('test', $model->getName());
    }
}