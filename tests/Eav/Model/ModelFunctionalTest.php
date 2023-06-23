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
use Doctrine\DBAL\Connection as DBALConnection;
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
     * @covers \Drobotik\Eav\Model\Model::connection
     */
    public function connection()
    {
        $this->assertInstanceOf(DBALConnection::class, $this->model->connection());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::getKey
     * @covers \Drobotik\Eav\Model\Model::setKey
     * @covers \Drobotik\Eav\Model\Model::isKey
     */
    public function key()
    {
        $this->assertFalse($this->model->isKey());
        $this->model->setKey(123);
        $this->assertTrue($this->model->isKey());
        $this->assertEquals(123, $this->model->getKey());
        $this->model->setKey(0);
        $this->assertFalse($this->model->isKey());
    }


    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::getKeyName
     * @covers \Drobotik\Eav\Model\Model::setKeyName
     */
    public function keyName()
    {
        $this->assertEquals('id', $this->model->getKeyName());
        $this->model->setKeyName('test');
        $this->assertEquals('test', $this->model->getKeyName());
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
            _DOMAIN::NAME->column() => 'test'
        ]);

        $this->assertEquals(1, $result);
        $this->assertEquals(1, $this->model->getKey());

        $connection = Connection::pdo();

        $stmt = $connection->prepare("SELECT * FROM $table");

        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _DOMAIN::ID->column() => 1,
            _DOMAIN::NAME->column() => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::update
     */
    public function update_record()
    {
        $table = _DOMAIN::table();
        $connection = Connection::pdo();

        $nameColumn = _DOMAIN::NAME->column();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();

        $key = (int) $connection->lastInsertId();

        $this->model->setKey($key);
        $this->model->setKeyName(_DOMAIN::ID->column());
        $this->model->setTable($table);
        $result = $this->model->update([
            _DOMAIN::NAME->column() => 'Jerry'
        ]);
        $this->assertTrue($result);

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _DOMAIN::ID->column() => 1,
                _DOMAIN::NAME->column() => 'Tom',
            ],
            [
                _DOMAIN::ID->column() => $key,
                _DOMAIN::NAME->column() => 'Jerry',
            ]
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::delete
     */
    public function delete_record()
    {
        $table = _DOMAIN::table();
        $connection = Connection::pdo();

        $nameColumn = _DOMAIN::NAME->column();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();

        $key = (int) $connection->lastInsertId();

        $this->model->setKey($key);
        $this->model->setKeyName(_DOMAIN::ID->column());
        $this->model->setTable($table);
        $result = $this->model->delete();

        $this->assertTrue($result);

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _DOMAIN::ID->column() => 1,
                _DOMAIN::NAME->column() => 'Tom',
            ]
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\Model::toArray
     */
    public function to_array()
    {
        $this->assertEquals([], $this->model->toArray());

        $this->model->setKey(123);
        $this->model->setKeyName('test');

        $this->assertEquals(['test' => 123], $this->model->toArray());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\Model::count
     */
    public function records_count()
    {
        $table = _DOMAIN::table();
        $connection = Connection::pdo();

        $nameColumn = _DOMAIN::NAME->column();
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
     * @covers \Drobotik\Eav\Model\Model::findMe
     */
    public function findMe()
    {
        $table = _DOMAIN::table();
        $connection = Connection::pdo();

        $idColumn = _DOMAIN::ID->column();
        $nameColumn = _DOMAIN::NAME->column();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Tom')");
        $stmt->execute();
        $stmt = $connection->prepare("INSERT INTO $table ($nameColumn) VALUES ('Jerry')");
        $stmt->execute();

        $this->model->setTable($table);
        $this->model->setKeyName($idColumn);
        $this->model->setKey(1);
        $result = $this->model->findMe();
        $this->assertEquals([
            $idColumn => 1,
            $nameColumn => "Tom"
        ], $result);

        $this->model->setKey(3);
        $result = $this->model->findMe();
        $this->assertFalse($result);
    }

}