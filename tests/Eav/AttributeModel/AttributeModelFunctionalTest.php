<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeModel;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeModel;
use PDO;
use Tests\TestCase;

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
     * @covers \Kuperwood\Eav\Model\AttributeModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_ATTR::table(), $this->model->getTable());
        $this->assertEquals(_ATTR::ID, $this->model->getPrimaryKey());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\AttributeModel::create
     */
    public function create_record()
    {
        $result = $this->model->create([
            _ATTR::DOMAIN_ID => 1,
            _ATTR::NAME => 'test',
            _ATTR::TYPE => ATTR_TYPE::STRING,
            _ATTR::STRATEGY => 'strategy',
            _ATTR::SOURCE => 'source',
            _ATTR::DEFAULT_VALUE => 'default',
            _ATTR::DESCRIPTION => 'description'
        ]);
        $this->assertEquals(1, $result);

        $table = _ATTR::table();
        $connection = Connection::get();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals([
            _ATTR::ID => 1,
            _ATTR::DOMAIN_ID => 1,
            _ATTR::NAME => 'test',
            _ATTR::TYPE => ATTR_TYPE::STRING,
            _ATTR::STRATEGY => 'strategy',
            _ATTR::SOURCE => 'source',
            _ATTR::DEFAULT_VALUE => 'default',
            _ATTR::DESCRIPTION => 'description'
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\AttributeModel::findByName
     */
    public function find_by_name()
    {
        $domainKey = 1;
        $name = "test";
        $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => $name
        ]);
        $this->eavFactory->createAttribute();
        $this->eavFactory->createAttribute();

        $this->assertFalse($this->model->findByName('Tom', $domainKey));
        $this->assertFalse($this->model->findByName('test', 123));
        $this->assertIsArray($this->model->findByName('test', $domainKey));
    }
}