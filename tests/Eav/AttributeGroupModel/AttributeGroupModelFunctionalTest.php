<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeGroupModel;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Model\AttributeGroupModel;
use PDO;
use Tests\TestCase;

class AttributeGroupModelFunctionalTest extends TestCase
{

    private AttributeGroupModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new AttributeGroupModel();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\AttributeGroupModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_GROUP::table(), $this->model->getTable());
        $this->assertEquals(_GROUP::ID, $this->model->getPrimaryKey());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\AttributeGroupModel::create
     */
    public function create_record()
    {
        $result = $this->model->create([
            _GROUP::SET_ID => 123,
            _GROUP::NAME => 'test'
        ]);
        $this->assertEquals(1, $result);

        $table = _GROUP::table();
        $connection = Connection::get();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals([
            _GROUP::ID => 1,
            _GROUP::SET_ID => 123,
            _GROUP::NAME => 'test'
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\AttributeGroupModel::checkGroupInAttributeSet
     */
    public function checkGroupKeyInAttributeSet()
    {
        $domainKey = $this->eavFactory->createDomain();
        $set1Key = $this->eavFactory->createAttributeSet($domainKey);
        $set2Key = $this->eavFactory->createAttributeSet($domainKey);
        $group1Key = $this->eavFactory->createGroup($set1Key);
        $group2Key = $this->eavFactory->createGroup($set1Key);
        $group3Key = $this->eavFactory->createGroup($set2Key);

        $this->assertTrue($this->model->checkGroupInAttributeSet($set1Key, $group1Key));
        $this->assertTrue($this->model->checkGroupInAttributeSet($set1Key, $group2Key));
        $this->assertFalse($this->model->checkGroupInAttributeSet($set1Key, $group3Key));
        $this->assertTrue($this->model->checkGroupInAttributeSet($set2Key, $group3Key));
        $this->assertFalse($this->model->checkGroupInAttributeSet($set2Key, $group1Key));
        $this->assertFalse($this->model->checkGroupInAttributeSet($set2Key, $group2Key));
    }
}