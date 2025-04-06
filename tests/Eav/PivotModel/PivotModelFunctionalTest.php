<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotModel;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Model\PivotModel;
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
     * @covers \Kuperwood\Eav\Model\PivotModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_PIVOT::table(), $this->model->getTable());
        $this->assertEquals(_PIVOT::ID, $this->model->getPrimaryKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\PivotModel::create
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
        $connection = Connection::get();

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
     * @covers \Kuperwood\Eav\Model\PivotModel::findOne
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