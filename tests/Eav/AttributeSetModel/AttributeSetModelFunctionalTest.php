<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetModel;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Model\AttributeSetModel;
use PDO;
use Tests\TestCase;


class AttributeSetModelFunctionalTest extends TestCase
{

    private AttributeSetModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new AttributeSetModel();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::__construct
     */
    public function defaults()
    {
        $this->assertEquals(_SET::table(), $this->model->getTable());
        $this->assertEquals(_SET::ID, $this->model->getPrimaryKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::create
     */
    public function create_record()
    {
        $result = $this->model->create([
            _SET::DOMAIN_ID => 1,
            _SET::NAME => 'test'
        ]);
        $this->assertEquals(1, $result);

        $table = _SET::table();
        $stmt = Connection::get()->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals([
            _SET::ID => 1,
            _SET::DOMAIN_ID => 1,
            _SET::NAME => 'test',
        ], $record);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\AttributeSetModel::findAttributes
     */
    public function findAttributes() {

        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attr1Key = $this->eavFactory->createAttribute();
        $attr2Key = $this->eavFactory->createAttribute();
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attr1Key);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attr2Key);

        $result = $this->model->findAttributes(123);
        $this->assertEquals([], $result);

        $sql = sprintf("SELECT * FROM %s", _ATTR::table()); // Using the table name from _ATTR
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $expected = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals(2, count($expected));
        $result = $this->model->findAttributes($domainKey, $setKey);
        $this->assertEquals($expected, $result);
    }

}