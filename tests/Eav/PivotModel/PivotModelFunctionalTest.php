<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotModel;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Model\PivotModel;
use PDO;
use Tests\TestCase;

class PivotModelFunctionalTest extends TestCase
{
    private PivotModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new PivotModel();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_PIVOT::table(), $this->model->getTable());
        $this->assertEquals(_PIVOT::ID, $this->model->getPrimaryKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::create
     */
    public function create_record()
    {
        $key = $this->model->create([
            _PIVOT::DOMAIN_ID => 1,
            _PIVOT::SET_ID => 2,
            _PIVOT::GROUP_ID => 3,
            _PIVOT::ATTR_ID => 4
        ]);
        $this->assertEquals(1, $key);

        $table = _PIVOT::table();
        $connection = Connection::get()->getNativeConnection();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals([
            _PIVOT::ID => 1,
            _PIVOT::DOMAIN_ID => 1,
            _PIVOT::SET_ID => 2,
            _PIVOT::GROUP_ID => 3,
            _PIVOT::ATTR_ID => 4
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\PivotModel::findOne
     */
    public function findOne()
    {
        $this->model->create([
            _PIVOT::DOMAIN_ID => 1,
            _PIVOT::SET_ID => 2,
            _PIVOT::GROUP_ID => 3,
            _PIVOT::ATTR_ID => 4
        ]);
        $this->assertIsArray($this->model->findOne(1,2,3,4));
        $this->assertFalse($this->model->findOne(2,2,3,4));
        $this->assertFalse($this->model->findOne(1,3,3,4));
        $this->assertFalse($this->model->findOne(1,2,4,4));
        $this->assertFalse($this->model->findOne(1,2,3,5));
    }
}