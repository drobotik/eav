<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Entity;

use Carbon\Carbon;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Drobotik\Eav\Result\EntityFactoryResult;
use Tests\TestCase;


class EntityAcceptanceTest extends TestCase
{
    /**
     * @test
     * @group acceptance
     * @covers \Drobotik\Eav\Entity::save()
     */
    public function creating_entities() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($setKey);
        $stringAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "string",
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ]);
        $integerAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "integer",
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ]);
        $decimalAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "decimal",
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);
        $datetimeAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "datetime",
            _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
        ]);
        $textAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "text",
            _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $group->getKey(), $stringAttribute->getKey());
        $this->eavFactory->createPivot($domainKey, $setKey, $group->getKey(), $integerAttribute->getKey());
        $this->eavFactory->createPivot($domainKey, $setKey, $group->getKey(), $decimalAttribute->getKey());
        $this->eavFactory->createPivot($domainKey, $setKey, $group->getKey(), $datetimeAttribute->getKey());
        $this->eavFactory->createPivot($domainKey, $setKey, $group->getKey(), $textAttribute->getKey());

        for($i = 0; $i < 2; $i++) {
            $message = "Failed on iteration $i";
            $data = [
                "string" => $this->faker->word,
                "integer" => $this->faker->randomNumber(),
                "decimal" => $this->faker->randomFloat(ATTR_TYPE::DECIMAL->migrateOptions()['scale']),
                "datetime" => Carbon::now()->format('Y-m-d H:i:s'),
                "text" => $this->faker->text
            ];
            $entity = new Entity();
            $entity->setDomainKey($domainKey);
            $entity->getAttributeSet()->setKey($setKey);
            $entity->getBag()->setFields($data);
            $entity->save();

            /** @var ValueStringModel $stringValue */
            /** @var ValueIntegerModel $integerValue */
            /** @var ValueDecimalModel $decimalValue */
            /** @var ValueDatetimeModel $datetimeValue */
            /** @var ValueTextModel $textValue */

            $stringValue = ValueStringModel::where(_VALUE::ENTITY_ID->column(), $entity->getKey())->first();
            $integerValue = ValueIntegerModel::where(_VALUE::ENTITY_ID->column(), $entity->getKey())->first();
            $decimalValue = ValueDecimalModel::where(_VALUE::ENTITY_ID->column(), $entity->getKey())->first();
            $datetimeValue = ValueDatetimeModel::where(_VALUE::ENTITY_ID->column(), $entity->getKey())->first();
            $textValue = ValueTextModel::where(_VALUE::ENTITY_ID->column(), $entity->getKey())->first();

            $this->assertNotNull($stringValue, $message);
            $this->assertNotNull($integerValue, $message);
            $this->assertNotNull($decimalValue, $message);
            $this->assertNotNull($datetimeValue, $message);
            $this->assertNotNull($textValue, $message);

            $this->assertEquals($domainKey, $stringValue->getDomainKey(), $message);
            $this->assertEquals($domainKey, $integerValue->getDomainKey(), $message);
            $this->assertEquals($domainKey, $decimalValue->getDomainKey(), $message);
            $this->assertEquals($domainKey, $datetimeValue->getDomainKey(), $message);
            $this->assertEquals($domainKey, $textValue->getDomainKey(), $message);

            $this->assertEquals($stringAttribute->getKey(), $stringValue->getAttrKey(), $message);
            $this->assertEquals($integerAttribute->getKey(), $integerValue->getAttrKey(), $message);
            $this->assertEquals($decimalAttribute->getKey(), $decimalValue->getAttrKey(), $message);
            $this->assertEquals($datetimeAttribute->getKey(), $datetimeValue->getAttrKey(), $message);
            $this->assertEquals($textAttribute->getKey(), $textValue->getAttrKey(), $message);

            $this->assertEquals($data["string"], $stringValue->getValue(), $message);
            $this->assertEquals($data["integer"], $integerValue->getValue(), $message);
            $this->assertEquals($data["decimal"], $decimalValue->getValue(), $message);
            $this->assertEquals($data["datetime"], $datetimeValue->getValue(), $message);
            $this->assertEquals($data["text"], $textValue->getValue(), $message);

            $set = $entity->getAttributeSet();
            foreach($data as $name => $value) {
                $this->assertTrue($set->hasContainer($name));
                $container = $set->getContainer($name);
                $this->assertEquals($value, $container->getValueManager()->getStored());
            }
            $this->assertEquals($data, $entity->toArray());
        }
    }

    /**
     * @test
     * @group acceptance
     * @covers \Drobotik\Eav\Entity::save()
     */
    public function find_and_update() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "string",
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "integer",
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "decimal",
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "datetime",
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "text",
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "text value"
            ]
        ];
        /** @var EntityFactoryResult $result */
        $result = $this->eavFactory->createEavEntity($fields, $domainKey, $setKey)->getData();
        $entityKey = $result->getEntityKey();

        $entity = new Entity();
        $entity
            ->setKey($entityKey)
            ->setDomainKey($domainKey)
            ->getAttributeSet()->setKey($setKey);
        $entity->find();

        $stringValue = "new string value";
        $integerValue = 321;
        $decimalValue = 3.05;
        $datetimeValue = Carbon::yesterday()->format('Y-m-d H:i:s');
        $textValue = "new text value";

        $bag = $entity->getBag();

        $bag->setField("string", $stringValue);
        $bag->setField("integer", $integerValue);
        $bag->setField("decimal", $decimalValue);
        $bag->setField("datetime", $datetimeValue);
        $bag->setField("text", $textValue);

        $entity->save();
        $attrSet = $entity->getAttributeSet();

        // check string
        $stringRecord = ValueStringModel::query()->where(_VALUE::ENTITY_ID->column(), $entity->getKey())->firstOrFail();
        $this->assertEquals($stringValue, $stringRecord->getValue());
        $this->assertEquals($stringValue, $attrSet->getContainer("string")->getValueManager()->getStored());
        // check integer
        $integerRecord = ValueIntegerModel::query()->where(_VALUE::ENTITY_ID->column(), $entity->getKey())->firstOrFail();
        $this->assertEquals($integerValue, $integerRecord->getValue());
        $this->assertEquals($integerValue, $attrSet->getContainer("integer")->getValueManager()->getStored());
        // check decimal
        $decimalRecord = ValueDecimalModel::query()->where(_VALUE::ENTITY_ID->column(), $entity->getKey())->firstOrFail();
        $this->assertEquals($decimalValue, $decimalRecord->getValue());
        $this->assertEquals($decimalValue, $attrSet->getContainer("decimal")->getValueManager()->getStored());
        // check datetime
        $datetimeRecord = ValueDatetimeModel::query()->where(_VALUE::ENTITY_ID->column(), $entity->getKey())->firstOrFail();
        $this->assertEquals($datetimeValue, $datetimeRecord->getValue());
        $this->assertEquals($datetimeValue, $attrSet->getContainer("datetime")->getValueManager()->getStored());
        // check text
        $textRecord = ValueTextModel::query()->where(_VALUE::ENTITY_ID->column(), $entity->getKey())->firstOrFail();
        $this->assertEquals($textValue, $textRecord->getValue());
        $this->assertEquals($textValue, $attrSet->getContainer("text")->getValueManager()->getStored());
    }

    /**
     * @test
     * @group acceptance
     * @covers \Drobotik\Eav\Entity::delete()
     */
    public function delete() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "string",
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "integer",
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "decimal",
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "datetime",
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "text",
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "text value"
            ]
        ];
        /** @var EntityFactoryResult $result */
        $result = $this->eavFactory->createEavEntity($fields, $domainKey, $setKey)->getData();
        $entityKey = $result->getEntityKey();

        $entity = new Entity();
        $entity
            ->setKey($entityKey)
            ->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);

        $entity->delete();

        $m = new EntityModel();
        $this->assertEquals(false, $m->findByKey($entityKey));
        $this->assertEquals(0, ValueStringModel::query()->where(_VALUE::ENTITY_ID->column(),  $entityKey)->count());
        $this->assertEquals(0, ValueIntegerModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueDecimalModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueDatetimeModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueTextModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());

        $this->assertFalse($entity->hasKey());
        $this->assertEquals(0, $entity->getKey());
        $this->assertFalse($entity->hasDomainKey());
        $this->assertEquals(0, $entity->getKey());
        $this->assertEquals([], $entity->getBag()->getData());

        $attrSet = $entity->getAttributeSet();
        $this->assertFalse($attrSet->hasKey());
        $this->assertEquals([], $attrSet->getContainers());
    }

    /**
     * @test
     * @group acceptance
     * @covers \Drobotik\Eav\Entity::delete()
     */
    public function find_and_delete() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "string",
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "integer",
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "decimal",
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "datetime",
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "text",
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey(),
                ATTR_FACTORY::VALUE->field() => "text value"
            ]
        ];
        /** @var EntityFactoryResult $result */
        $result = $this->eavFactory->createEavEntity($fields, $domainKey, $setKey)->getData();
        $entityKey = $result->getEntityKey();

        $entity = new Entity();
        $entity
            ->setKey($entityKey)
            ->setDomainKey($domainKey)
            ->getAttributeSet()->setKey($setKey);
        $entity->find();

        $this->assertSameSize($fields, $entity->toArray());

        $entity->delete();

        $model = new EntityModel();
        $this->assertEquals(false, $model->findByKey($entityKey));
        $this->assertEquals(0, ValueStringModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueIntegerModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueDecimalModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueDatetimeModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());
        $this->assertEquals(0, ValueTextModel::query()->where(_VALUE::ENTITY_ID->column(), $entityKey)->count());

        $this->assertFalse($entity->hasKey());
        $this->assertEquals(0, $entity->getKey());
        $this->assertFalse($entity->hasDomainKey());
        $this->assertEquals(0, $entity->getKey());
        $this->assertEquals([], $entity->getBag()->getData());

        $attrSet = $entity->getAttributeSet();
        $this->assertFalse($attrSet->hasKey());
        $this->assertEquals([], $attrSet->getContainers());
    }
}