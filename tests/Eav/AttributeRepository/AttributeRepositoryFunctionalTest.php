<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeRepository;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Repository\AttributeRepository;
use Tests\TestCase;

class AttributeRepositoryFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Repository\AttributeRepository::getLinked
     */
    public function get_linked()
    {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $fields = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "string",
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => "string value"
            ],
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => "integer",
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey,
                ATTR_FACTORY::VALUE->field() => 123
            ]
        ];

        $this->eavFactory->createEavEntity($fields, $domainKey, $setKey);

        $repository = new AttributeRepository();
        $attributes = $repository->getLinked($domainKey, $setKey);

        $this->assertEquals(2, $attributes->count());
        /** @var AttributeModel $attr1 */
        $attr1 = $attributes[0];
        /** @var AttributeModel $attr2 */
        $attr2 = $attributes[1];
        $this->assertEquals('string', $attr1->getName());
        $this->assertEquals($domainKey, $attr1->getDomainKey());
        $this->assertEquals($setKey, $attr1->{_PIVOT::SET_ID->column()});
        $this->assertEquals($groupKey, $attr1->{_PIVOT::GROUP_ID->column()});

        $this->assertEquals('integer', $attr2->getName());
        $this->assertEquals($domainKey, $attr2->getDomainKey());
        $this->assertEquals($setKey, $attr2->{_PIVOT::SET_ID->column()});
        $this->assertEquals($groupKey, $attr2->{_PIVOT::GROUP_ID->column()});
    }
}