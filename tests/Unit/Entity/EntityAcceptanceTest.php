<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Carbon\Carbon;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;
use Tests\TestCase;


class EntityAcceptanceTest extends TestCase
{
    /**
     * @test
     * @group acceptance
     * @covers
     */
    public function creating_entities() {
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $group = $this->eavFactory->createGroup($attrSet);
        $stringAttribute = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "string",
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ]);
        $integerAttribute = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "integer",
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ]);
        $decimalAttribute = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "decimal",
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);
        $datetimeAttribute = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "datetime",
            _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
        ]);
        $textAttribute = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "text",
            _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
        ]);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $stringAttribute);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $integerAttribute);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $decimalAttribute);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $datetimeAttribute);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $textAttribute);

        for($i = 0; $i < 2; $i++) {
            $message = "Failed on iteration $i";
            $data = [
                "string" => $this->faker->word,
                "integer" => $this->faker->randomNumber(),
                "decimal" => $this->faker->randomFloat(),
                "datetime" => Carbon::now()->format('Y-m-d H:i:s'),
                "text" => $this->faker->text
            ];
            $entity = new Entity();
            $entity->setDomainKey($domain->getKey());
            $entity->getAttributeSet()->setKey($attrSet->getKey());
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

            $this->assertEquals($domain->getKey(), $stringValue->getDomainKey(), $message);
            $this->assertEquals($domain->getKey(), $integerValue->getDomainKey(), $message);
            $this->assertEquals($domain->getKey(), $decimalValue->getDomainKey(), $message);
            $this->assertEquals($domain->getKey(), $datetimeValue->getDomainKey(), $message);
            $this->assertEquals($domain->getKey(), $textValue->getDomainKey(), $message);

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
}