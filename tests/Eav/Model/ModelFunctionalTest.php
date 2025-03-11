<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Model;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Model\Model;
use PDO;
use Tests\TestCase;

class ModelFunctionalTest extends TestCase
{

    private Model $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Model();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::db
     */
    public function db()
    {
        $this->assertInstanceOf(PDO::class, $this->model->db());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::getPrimaryKey
     * @covers \Drobotik\Eav\Model\Model::setPrimaryKey
     */
    public function keyName()
    {
        $this->assertEquals('id', $this->model->getPrimaryKey());
        $this->model->setPrimaryKey('test');
        $this->assertEquals('test', $this->model->getPrimaryKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::getTable
     * @covers \Drobotik\Eav\Model\Model::setTable
     */
    public function table()
    {
        $this->model->setTable('test');
        $this->assertEquals('test', $this->model->getTable());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::insert
     */
    public function insert_record()
    {
        $table = _DOMAIN::table();

        $this->model->setTable($table);
        $result = $this->model->insert([
            _DOMAIN::NAME => 'test'
        ]);

        $this->assertEquals(1, $result);

        $connection = Connection::get();

        $stmt = $connection->prepare("SELECT * FROM $table");

        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _DOMAIN::ID => 1,
            _DOMAIN::NAME => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::updateByArray
     */
    public function update_record()
    {
        $table = _DOMAIN::table();
        $connection = Connection::get();

        $nameColumn = _DOMAIN::NAME;
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();

        $key = (int) $connection->lastInsertId();

        $this->model->setPrimaryKey(_DOMAIN::ID);
        $this->model->setTable($table);
        $result = $this->model->updateByArray($key, [
            _DOMAIN::NAME => 'Jerry'
        ]);
        $this->assertTrue($result);

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _DOMAIN::ID => 1,
                _DOMAIN::NAME => 'Tom',
            ],
            [
                _DOMAIN::ID => $key,
                _DOMAIN::NAME => 'Jerry',
            ]
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::deleteByKey
     */
    public function delete_record()
    {
        $table = _DOMAIN::table();
        $connection = Connection::get();

        $nameColumn = _DOMAIN::NAME;
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();

        $key = (int) $connection->lastInsertId();

        $this->model->setPrimaryKey(_DOMAIN::ID);
        $this->model->setTable($table);
        $result = $this->model->deleteByKey($key);

        $this->assertTrue($result);

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _DOMAIN::ID => 1,
                _DOMAIN::NAME => 'Tom',
            ]
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\Model::count
     */
    public function records_count()
    {
        $table = _DOMAIN::table();
        $connection = Connection::get();

        $nameColumn = _DOMAIN::NAME;
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();

        $this->model->setTable($table);

        $this->assertEquals(2, $this->model->count());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\Model::findByKey
     */
    public function find_by_key()
    {
        $table = _DOMAIN::table();
        $connection = Connection::get();

        $idColumn = _DOMAIN::ID;
        $nameColumn = _DOMAIN::NAME;
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Jerry')");
        $stmt->execute();

        $this->model->setTable($table);
        $this->model->setPrimaryKey($idColumn);
        $result = $this->model->findByKey(1);
        $this->assertEquals([
            $idColumn => 1,
            $nameColumn => "Tom"
        ], $result);

        $result = $this->model->findByKey(3);
        $this->assertFalse($result);
    }

}