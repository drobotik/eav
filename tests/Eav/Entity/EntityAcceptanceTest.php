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
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\EntityModel;
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
        $groupKey = $this->eavFactory->createGroup($setKey);
        $valueModel = $this->makeValueModel();

        $stringAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "string",
            _ATTR::TYPE => ATTR_TYPE::STRING->value()
        ]);
        $integerAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "integer",
            _ATTR::TYPE => ATTR_TYPE::INTEGER->value()
        ]);
        $decimalAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "decimal",
            _ATTR::TYPE => ATTR_TYPE::DECIMAL->value()
        ]);
        $datetimeAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "datetime",
            _ATTR::TYPE => ATTR_TYPE::DATETIME->value()
        ]);
        $textAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "text",
            _ATTR::TYPE => ATTR_TYPE::TEXT->value()
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $stringAttributeKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $integerAttributeKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $decimalAttributeKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $datetimeAttributeKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $textAttributeKey);

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

            $entityKey = $entity->getKey();

            $stringValue = $valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $stringAttributeKey);
            $integerValue = $valueModel->find(ATTR_TYPE::INTEGER->valueTable(), $domainKey, $entityKey, $integerAttributeKey);
            $decimalValue = $valueModel->find(ATTR_TYPE::DECIMAL->valueTable(), $domainKey, $entityKey, $decimalAttributeKey);
            $datetimeValue = $valueModel->find(ATTR_TYPE::DATETIME->valueTable(), $domainKey, $entityKey, $datetimeAttributeKey);
            $textValue = $valueModel->find(ATTR_TYPE::TEXT->valueTable(), $domainKey, $entityKey, $textAttributeKey);

            $this->assertIsArray($stringValue, $message);
            $this->assertIsArray($integerValue, $message);
            $this->assertIsArray($decimalValue, $message);
            $this->assertIsArray($datetimeValue, $message);
            $this->assertIsArray($textValue, $message);

            $this->assertEquals($data["string"], $stringValue[_VALUE::VALUE->column()], $message);
            $this->assertEquals($data["integer"], $integerValue[_VALUE::VALUE->column()], $message);
            $this->assertEquals($data["decimal"], $decimalValue[_VALUE::VALUE->column()], $message);
            $this->assertEquals($data["datetime"], $datetimeValue[_VALUE::VALUE->column()], $message);
            $this->assertEquals($data["text"], $textValue[_VALUE::VALUE->column()], $message);

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
        $groupKey = $this->eavFactory->createGroup($setKey);
        $valueModel = $this->makeValueModel();
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::INTEGER->value(),
                    _ATTR::TYPE => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::TYPE => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DATETIME->value(),
                    _ATTR::TYPE => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::TEXT->value(),
                    _ATTR::TYPE => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
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

        $bag->setField(ATTR_TYPE::STRING->value(), $stringValue);
        $bag->setField(ATTR_TYPE::INTEGER->value(), $integerValue);
        $bag->setField(ATTR_TYPE::DECIMAL->value(), $decimalValue);
        $bag->setField(ATTR_TYPE::DATETIME->value(), $datetimeValue);
        $bag->setField(ATTR_TYPE::TEXT->value(), $textValue);

        $entity->save();
        $attrSet = $entity->getAttributeSet();
        $attributes = $result->getAttributes();

        $stringAttrKey = $attributes[ATTR_TYPE::STRING->value()][_ATTR::ID];
        $integerAttrKey = $attributes[ATTR_TYPE::INTEGER->value()][_ATTR::ID];
        $decimalAttrKey = $attributes[ATTR_TYPE::DECIMAL->value()][_ATTR::ID];
        $datetimeAttrKey = $attributes[ATTR_TYPE::DATETIME->value()][_ATTR::ID];
        $textAttrKey = $attributes[ATTR_TYPE::TEXT->value()][_ATTR::ID];
        // check string
        $stringRecord = $valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $stringAttrKey);
        $this->assertEquals($stringValue, $stringRecord[_VALUE::VALUE->column()]);
        $this->assertEquals($stringValue, $attrSet->getContainer(ATTR_TYPE::STRING->value())->getValueManager()->getStored());
        // check integer
        $integerRecord = $valueModel->find(ATTR_TYPE::INTEGER->valueTable(), $domainKey, $entityKey, $integerAttrKey);
        $this->assertEquals($integerValue, $integerRecord[_VALUE::VALUE->column()]);
        $this->assertEquals($integerValue, $attrSet->getContainer(ATTR_TYPE::INTEGER->value())->getValueManager()->getStored());
        // check decimal
        $decimalRecord = $valueModel->find(ATTR_TYPE::DECIMAL->valueTable(), $domainKey, $entityKey, $decimalAttrKey);
        $this->assertEquals($decimalValue, $decimalRecord[_VALUE::VALUE->column()]);
        $this->assertEquals($decimalValue, $attrSet->getContainer(ATTR_TYPE::DECIMAL->value())->getValueManager()->getStored());
        // check datetime
        $datetimeRecord = $valueModel->find(ATTR_TYPE::DATETIME->valueTable(), $domainKey, $entityKey, $datetimeAttrKey);
        $this->assertEquals($datetimeValue, $datetimeRecord[_VALUE::VALUE->column()]);
        $this->assertEquals($datetimeValue, $attrSet->getContainer(ATTR_TYPE::DATETIME->value())->getValueManager()->getStored());
        // check text
        $textRecord = $valueModel->find(ATTR_TYPE::TEXT->valueTable(), $domainKey, $entityKey, $textAttrKey);
        $this->assertEquals($textValue, $textRecord[_VALUE::VALUE->column()]);
        $this->assertEquals($textValue, $attrSet->getContainer(ATTR_TYPE::TEXT->value())->getValueManager()->getStored());
    }

    /**
     * @test
     * @group acceptance
     * @covers \Drobotik\Eav\Entity::delete()
     */
    public function delete() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::INTEGER->value(),
                    _ATTR::TYPE => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::TYPE => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DATETIME->value(),
                    _ATTR::TYPE => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::TEXT->value(),
                    _ATTR::TYPE => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() =>$groupKey,
                ATTR_FACTORY::VALUE->field() => "text value"
            ]
        ];
        /** @var EntityFactoryResult $result */
        $result = $this->eavFactory->createEavEntity($fields, $domainKey, $setKey)->getData();

        $attributes = $result->getAttributes();


        $entityKey = $result->getEntityKey();

        $entity = new Entity();
        $entity
            ->setKey($entityKey)
            ->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);

        $entity->delete();

        $valueModel = $this->makeValueModel();

        $entityModel = new EntityModel();
        $this->assertEquals(false, $entityModel->findByKey($entityKey));
        $this->assertFalse($valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::STRING->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::INTEGER->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::INTEGER->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::DECIMAL->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::DECIMAL->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::DATETIME->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::DATETIME->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::TEXT->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::TEXT->value()][_ATTR::ID]));

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
        $groupKey = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::INTEGER->value(),
                    _ATTR::TYPE => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 123
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::TYPE => ATTR_TYPE::DECIMAL->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 3.14
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::DATETIME->value(),
                    _ATTR::TYPE => ATTR_TYPE::DATETIME->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME => ATTR_TYPE::TEXT->value(),
                    _ATTR::TYPE => ATTR_TYPE::TEXT->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => "text value"
            ]
        ];
        /** @var EntityFactoryResult $result */
        $result = $this->eavFactory->createEavEntity($fields, $domainKey, $setKey)->getData();
        $attributes = $result->getAttributes();

        $entityKey = $result->getEntityKey();

        $entity = new Entity();
        $entity
            ->setKey($entityKey)
            ->setDomainKey($domainKey)
            ->getAttributeSet()->setKey($setKey);
        $entity->find();

        $this->assertSameSize($fields, $entity->toArray());

        $entity->delete();

        $valueModel = $this->makeValueModel();
        $model = new EntityModel();
        $this->assertEquals(false, $model->findByKey($entityKey));
        $this->assertFalse($valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::STRING->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::INTEGER->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::INTEGER->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::DECIMAL->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::DECIMAL->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::DATETIME->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::DATETIME->value()][_ATTR::ID]));
        $this->assertFalse($valueModel->find(ATTR_TYPE::TEXT->valueTable(), $domainKey, $entityKey, $attributes[ATTR_TYPE::TEXT->value()][_ATTR::ID]));

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