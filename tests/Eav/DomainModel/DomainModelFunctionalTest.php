<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DomainModel;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Model\DomainModel;
use PDO;
use Tests\TestCase;

class DomainModelFunctionalTest extends TestCase
{
    private DomainModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new DomainModel();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_DOMAIN::table(), $this->model->getTable());
        $this->assertEquals(_DOMAIN::ID->column(), $this->model->getKeyName());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::setName
     * @covers \Drobotik\Eav\Model\DomainModel::getName
     */
    public function name() {
        $this->model->setName('test');
        $this->assertEquals('test', $this->model->getName());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::create
     */
    public function create_record()
    {
        $this->model->setName('test');
        $result = $this->model->create();
        $this->assertEquals(1, $result);

        $table = _DOMAIN::table();
        $connection = Connection::get()->getNativeConnection();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _DOMAIN::ID->column() => 1,
                _DOMAIN::NAME->column() => 'test',
            ]
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::toArray
     */
    public function to_array()
    {
        $this->model->setKey(123);
        $this->model->setName('name');

        $this->assertEquals([
            _DOMAIN::ID->column() => 123,
            _DOMAIN::NAME->column() => 'name'
        ], $this->model->toArray());
    }

}