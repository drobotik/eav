<?php

declare(strict_types=1);

namespace Tests\Unit\DomainModel;

use Drobotik\Eav\Model\DomainModel;
use PHPUnit\Framework\TestCase;

class DomainModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers DomainModel::setName, DomainModel::getName,
     */
    public function name_accessor() {
        $model = new DomainModel();
        $model->setName('test');
        $this->assertEquals('test', $model->getName());
    }
}