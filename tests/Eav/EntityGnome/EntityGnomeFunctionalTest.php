<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityGnome;

use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityGnome;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Value\ValueParser;
use Tests\TestCase;

class EntityGnomeFunctionalTest extends TestCase
{
    private EntityGnome $gnome;
    public function setUp(): void
    {
        parent::setUp();
        $entity = new Entity();
        $this->gnome = new EntityGnome($entity);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::getEntity
     * @covers \Kuperwood\Eav\EntityGnome::__construct
     */
    public function entity()
    {
        $entity = new Entity();
        $gnome = new EntityGnome($entity);
        $this->assertSame($entity, $gnome->getEntity());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::find
     */
    public function find_no_key() {
        $result = $this->gnome->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::find
     */
    public function find_no_record() {
        $this->gnome->getEntity()->setKey(123);
        $result = $this->gnome->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_FOUND), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_no_entity_key_no_domain() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_DOMAIN_KEY);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_no_entity_key_no_attr_set_key() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        $this->gnome->getEntity()->setDomainKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     * @covers \Kuperwood\Eav\EntityGnome::checkDomainExists
     */
    public function save_no_entity_key_domain_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey(1);
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     * @covers \Kuperwood\Eav\EntityGnome::checkAttrSetExist
     */
    public function save_no_entity_key_attr_set_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domainKey = $this->eavFactory->createDomain();
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_no_entity_key_creating_record() {
        $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet();
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);

        $this->gnome->save();

        $model = new EntityModel();
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain($domainKey, $setKey)[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_no_record_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ENTITY_NOT_FOUND);
        $this->gnome->getEntity()->setKey(123);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_no_domain_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = new EntityModel();
        $entityKey = $entity->create([
            _ENTITY::DOMAIN_ID => 123,
            _ENTITY::ATTR_SET_ID => 123
        ]);
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_no_attr_set_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domainKey = $this->eavFactory->createDomain();
        $entity = new EntityModel();
        $entityKey = $entity->create([
            _ENTITY::DOMAIN_ID => $domainKey,
            _ENTITY::ATTR_SET_ID => 123
        ]);
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_fetching_record() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $entityKey = $this->eavFactory->createEntity($domainKey, $setKey);
        $entity = $this->gnome->getEntity();
        $entity->setKey($entityKey);

        $this->gnome->save();

        $model = new EntityModel();
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain($domainKey, $setKey)[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::save
     * @covers \Kuperwood\Eav\EntityGnome::beforeSave
     */
    public function save_results() {
        $testData = [
            "email" => "test@email.net",
            'phone' => '1234567890',
        ];
        $valueModel = $this->makeValueModel();
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attrEmailKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "email"
        ]);
        $attrPhoneKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "phone"
        ]);
        $attrNoteKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "note"
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrEmailKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrPhoneKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrNoteKey);

        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $set = $entity->getAttributeSet();
        $set->setKey($setKey);
        $bag = $entity->getBag();
        $bag->setFields($testData);

        $result = $this->gnome->save();

        $emailRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrEmailKey
        );
        $this->assertIsArray($emailRecord);
        $this->assertEquals($testData["email"], $emailRecord[_VALUE::VALUE]);

        $phoneRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrPhoneKey
        );
        $this->assertIsArray($phoneRecord);
        $this->assertEquals($testData["phone"], $phoneRecord[_VALUE::VALUE]);

        $noteRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrNoteKey
        );
        $this->assertFalse($noteRecord);

        $this->assertEquals([
            "email" => "test@email.net",
            'phone' => '1234567890',
            'note' => null
        ], $entity->getBag()->getData());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());

        $resultData = $result->getData();
        $this->assertCount(3, $resultData);

        $this->assertArrayHasKey('email', $resultData);
        $data = $resultData['email'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $data->getMessage());

        $this->assertArrayHasKey('phone', $resultData);
        $data = $resultData['phone'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $data->getMessage());

        $this->assertArrayHasKey('note', $resultData);
        $data = $resultData['note'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::EMPTY, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $data->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::delete
     */
    public function delete_exception_if_not_set_key_provided() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        $entity = $this->gnome->getEntity();
        $entity->setKey(1);
        $this->gnome->delete();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::delete
     */
    public function delete_exception_if_not_entity_key_provided() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ENTITY_KEY);
        $entity = $this->gnome->getEntity();
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->delete();
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::toArray
     * @covers \Kuperwood\Eav\EntityGnome::find
     * @covers \Kuperwood\Eav\Entity::getBag
     */
    public function find()
    {
        $eavFactory = $this->eavFactory;
        $domainKey = $eavFactory->createDomain();
        $setKey = $eavFactory->createAttributeSet($domainKey);
        $groupKey = $eavFactory->createGroup($setKey);
        $eavFactory->createEntity($domainKey, $setKey);
        $stringAttributeKey = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "string",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);
        $integerAttributeKey = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "integer",
            _ATTR::TYPE => ATTR_TYPE::INTEGER
        ]);
        $decimalAttributeKey = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "decimal",
            _ATTR::TYPE => ATTR_TYPE::DECIMAL
        ]);
        $datetimeAttributeKey = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "datetime",
            _ATTR::TYPE => ATTR_TYPE::DATETIME
        ]);
        $textAttributeKey = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "text",
            _ATTR::TYPE => ATTR_TYPE::TEXT
        ]);
        $eavFactory->createPivot($domainKey, $setKey, $groupKey, $stringAttributeKey);
        $eavFactory->createPivot($domainKey, $setKey, $groupKey, $integerAttributeKey);
        $eavFactory->createPivot($domainKey, $setKey, $groupKey, $decimalAttributeKey);
        $eavFactory->createPivot($domainKey, $setKey, $groupKey, $datetimeAttributeKey);
        $eavFactory->createPivot($domainKey, $setKey, $groupKey, $textAttributeKey);
        $data = [
            "string" => $this->faker->word,
            "integer" => $this->faker->randomNumber(),
            "decimal" => $this->faker->randomFloat(3),
            "datetime" => ATTR_TYPE::randomValue(ATTR_TYPE::DATETIME),
            "text" => $this->faker->text
        ];
        $entity = new Entity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);
        $entity->getBag()->setFields($data);
        $entity->save();
        $entityKey = $entity->getKey();

        // SETUP
        $entityModel = new Entity();
        $entityModel->setKey($entityKey);
        $entityModel->setDomainKey($domainKey);
        $entityModel ->getAttributeSet()->setKey($setKey);
        $entityModel->find();
        $entityData = $entityModel->toArray();

        $valueParser = new ValueParser();

        $expectedData = [
            'string' => $valueParser->parse(ATTR_TYPE::STRING, $data['string']),
            'integer' => $valueParser->parse( ATTR_TYPE::INTEGER, $data['integer']),
            'decimal' => $valueParser->parse(ATTR_TYPE::DECIMAL, $data['decimal']),
            'datetime' => $valueParser->parse(ATTR_TYPE::DATETIME, $data['datetime']),
            'text' => $valueParser->parse(ATTR_TYPE::TEXT, $data['text'])
        ];

        $this->assertEquals($expectedData, $entityData);

        $bag = $entityModel->getBag();
        $this->assertEquals($expectedData, $bag->getData());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityGnome::toArray
     */
    public function to_array_with_groups()
    {
        $eavFactory = $this->eavFactory;
        $domainKey = $eavFactory->createDomain();
        $setKey = $eavFactory->createAttributeSet($domainKey);
        $group1Key = $eavFactory->createGroup($setKey, [_GROUP::NAME => 'group1']);
        $group2Key = $eavFactory->createGroup($setKey, [_GROUP::NAME => 'group2']);

        $group1 = [
            _GROUP::ID => (string) $group1Key,
            _GROUP::SET_ID => (string) $setKey,
            _GROUP::NAME => 'group1',
        ];

        $group2 = [
            _GROUP::ID => (string) $group2Key,
            _GROUP::SET_ID => (string) $setKey,
            _GROUP::NAME => 'group2',
        ];

        $attr1Key = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "attr1",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);
        $attr2Key = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "attr2",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);
        $attr3Key = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "attr3",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);

        $defaultAttrData = [
            _ATTR::ID => null,
            _ATTR::DOMAIN_ID => (string) $domainKey,
            _ATTR::NAME => $this->faker->slug(2),
            _ATTR::TYPE => _ATTR::bag(_ATTR::TYPE),
            _ATTR::STRATEGY => _ATTR::bag(_ATTR::STRATEGY),
            _ATTR::SOURCE => _ATTR::bag(_ATTR::SOURCE),
            _ATTR::DEFAULT_VALUE => _ATTR::bag(_ATTR::DEFAULT_VALUE),
            _ATTR::DESCRIPTION => _ATTR::bag(_ATTR::DESCRIPTION),
            _ATTR::GROUP_ID => null
        ];

        $attr1 = $defaultAttrData;
        $attr1[_ATTR::ID] = (string) $attr1Key;
        $attr1[_ATTR::NAME] = 'attr1';
        $attr1[_ATTR::TYPE] = ATTR_TYPE::STRING;
        $attr1[_ATTR::GROUP_ID] = (string) $group1Key;

        $attr2 = $defaultAttrData;
        $attr2[_ATTR::ID] = (string) $attr2Key;
        $attr2[_ATTR::NAME] = 'attr2';
        $attr2[_ATTR::TYPE] = ATTR_TYPE::STRING;
        $attr2[_ATTR::GROUP_ID] = (string) $group2Key;

        $attr3 = $defaultAttrData;
        $attr3[_ATTR::ID] = (string) $attr3Key;
        $attr3[_ATTR::NAME] = 'attr3';
        $attr3[_ATTR::TYPE] = ATTR_TYPE::STRING;
        $attr3[_ATTR::GROUP_ID] = (string) $group2Key;

        $eavFactory->createPivot($domainKey, $setKey, $group1Key, $attr1Key);
        $eavFactory->createPivot($domainKey, $setKey, $group2Key, $attr2Key);
        $eavFactory->createPivot($domainKey, $setKey, $group2Key, $attr3Key);

        $data = [
            "attr1" => 'value1',
            "attr2" => 'value2',
            "attr3" => 'value3'
        ];
        $entity = new Entity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);
        $entity->getBag()->setFields($data);
        $entity->save();

        $result = $entity->getGnome()->toArrayByGroup();
        $this->assertSame([
            $group1Key => [
                'group' => $group1,
                'attributes' => [
                    [
                        'attribute' => $attr1,
                        'value' => 'value1'
                    ]
                ]
            ],
            $group2Key => [
                'group' => $group2,
                'attributes' => [
                    [
                        'attribute' => $attr2,
                        'value' => 'value2'
                    ],
                    [
                        'attribute' => $attr3,
                        'value' => 'value3'
                    ]
                ]
            ],
        ], $result);
    }
}