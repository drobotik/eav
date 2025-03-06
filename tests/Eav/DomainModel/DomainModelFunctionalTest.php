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
        $this->assertEquals(_DOMAIN::ID, $this->model->getPrimaryKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\DomainModel::create
     */
    public function create_record()
    {
        $result = $this->model->create([
            _DOMAIN::NAME => 'test'
        ]);
        $this->assertEquals(1, $result);

        $table = _DOMAIN::table();
        $connection = Connection::get()->getNativeConnection();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals([
            _DOMAIN::ID => 1,
            _DOMAIN::NAME => 'test',
        ], $record);
    }

}