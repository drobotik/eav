<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityModel;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Model\EntityModel;
use Faker\Generator;
use PDO;
use Tests\TestCase;

class EntityModelFunctionalTest extends TestCase
{
    private EntityModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new EntityModel();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::__construct
     */
    public function defaults() {
        $this->assertEquals(_ENTITY::table(), $this->model->getTable());
        $this->assertEquals(_ENTITY::ID, $this->model->getPrimaryKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::create
     */
    public function create_record()
    {
        $domainKey = 345;
        $setKey = 567;

        $entityKey = $this->model->create([
            _ENTITY::DOMAIN_ID => $domainKey,
            _ENTITY::ATTR_SET_ID => $setKey
        ]);

        $this->assertEquals(1, $entityKey);

        $table = _ENTITY::table();
        $connection = Connection::get();

        $stmt = $connection->prepare("SELECT * FROM $table");
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([
            [
                _ENTITY::ID => $entityKey,
                _ENTITY::DOMAIN_ID => $domainKey,
                _ENTITY::ATTR_SET_ID => $setKey,
                _ENTITY::SERVICE_KEY => null
            ]
        ], $record);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::isServiceKey
     */
    public function is_service_key()
    {
        $table = _ENTITY::table();
        $connection = Connection::get();

        $domainKey = 1;
        $setKey = 2;
        $serviceKey = 456;

        $domainColumn = _ENTITY::DOMAIN_ID;
        $setColumn = _ENTITY::ATTR_SET_ID;
        $serviceKeyColumn = _ENTITY::SERVICE_KEY;

        $stmt = $connection->prepare("INSERT INTO $table ($domainColumn,$setColumn,$serviceKeyColumn) VALUES ($domainKey,$setKey,$serviceKey)");
        $stmt->execute();

        $this->assertTrue($this->model->isServiceKey($serviceKey));
        $this->assertFalse($this->model->isServiceKey(4567));
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::getByServiceKey
     */
    public function get_by_service_key()
    {
        $table = _ENTITY::table();
        $connection = Connection::get();

        $domainKey = 1;
        $setKey = 2;
        $serviceKey = 456;

        $domainColumn = _ENTITY::DOMAIN_ID;
        $setColumn = _ENTITY::ATTR_SET_ID;
        $serviceKeyColumn = _ENTITY::SERVICE_KEY;

        $stmt = $connection->prepare("INSERT INTO $table ($domainColumn,$setColumn,$serviceKeyColumn) VALUES ($domainKey,$setKey,$serviceKey)");
        $stmt->execute();

        $result = $this->model->getByServiceKey($serviceKey);
        $this->assertCount(1, $this->model->getByServiceKey($serviceKey));

        $this->assertEquals([
            [
                _ENTITY::ID => 1,
                $domainColumn => $domainKey,
                $setColumn => $setKey,
                $serviceKeyColumn => $serviceKey
            ]
        ], $result);

        $this->assertCount(0, $this->model->getByServiceKey(4567));
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::getServiceKey
     */
    public function create_new_service_key()
    {
        $key = 123;
        $faker = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['randomDigit'])->getMock();
        $faker->expects($this->once())->method('randomDigit')
            ->willReturn($key);
        $model = $this->getMockBuilder(EntityModel::class)
            ->onlyMethods(['isServiceKey', 'makeFakerGenerator'])
            ->getMock();
        $model->expects($this->once())->method('makeFakerGenerator')
            ->willReturn($faker);
        $model->expects($this->once())->method('isServiceKey')
            ->willReturn(false);
        $this->assertEquals($key, $model->getServiceKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::getServiceKey
     */
    public function existing_service_key()
    {
        $table = _ENTITY::table();
        $connection = Connection::get();

        $domainKey = 1;
        $setKey = 2;
        $serviceKey = 456;
        $newServiceKey = 9847;

        $domainColumn = _ENTITY::DOMAIN_ID;
        $setColumn = _ENTITY::ATTR_SET_ID;
        $serviceKeyColumn = _ENTITY::SERVICE_KEY;

        $stmt = $connection->prepare("INSERT INTO $table ($domainColumn,$setColumn,$serviceKeyColumn) VALUES ($domainKey,$setKey,$serviceKey)");
        $stmt->execute();

        $faker = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['randomDigit'])->getMock();
        $faker->expects($this->exactly(2))->method('randomDigit')
            ->willReturn($serviceKey, $newServiceKey);

        $model = $this->getMockBuilder(EntityModel::class)
            ->onlyMethods(['makeFakerGenerator'])
            ->getMock();
        $model->expects($this->exactly(2))->method('makeFakerGenerator')
            ->willReturn($faker, $faker);

        $this->assertEquals($newServiceKey, $model->getServiceKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Model\EntityModel::bulkCreate
     */
    public function bulk_create_entity_records() {
        $serviceKey = 123;
        $this->model->bulkCreate(100, 2, 3, $serviceKey);
        $entities = $this->model->getByServiceKey($serviceKey);
        $this->assertEquals(100, count($entities));
        /** @var EntityModel $entity */
        foreach($entities as $index => $entity) {
            $this->assertEquals(2, $entity[_ENTITY::DOMAIN_ID], "Iteration:".$index);
            $this->assertEquals(3, $entity[_ENTITY::ATTR_SET_ID], "Iteration:".$index);
        }
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Model\EntityModel::bulkCreate
     */
    public function bulk_create_entities_null_amount() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::MUST_BE_POSITIVE_AMOUNT);
        $this->model->bulkCreate(0, 1, 1, 1);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\EntityModel::getBySetAndDomain
     */
    public function get_by_setKey_and_domainKey()
    {
        $this->eavFactory->createEntity(2, 3);
        $this->assertEquals([], $this->model->getBySetAndDomain(1, 2));
        $result = $this->model->getBySetAndDomain(2,3);
        $this->assertEquals(1, count($result));
        $entity = $result[0];
        $this->assertEquals(1, $entity[_ENTITY::ID]);
        $this->assertEquals(2, $entity[_ENTITY::DOMAIN_ID]);
        $this->assertEquals(3, $entity[_ENTITY::ATTR_SET_ID]);
        $this->assertEquals(null, $entity[_ENTITY::SERVICE_KEY]);
    }

}