<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderManager;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Tests\TestCase;

class QueryBuilderManagerAcceptanceTest extends TestCase
{
    private QueryBuilderManager $manager;

    public function setUp(): void
    {
        parent::setUp();
        $this->manager = new QueryBuilderManager();
    }
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getStoredAttributes
     */
    public function domain_key()
    {
        $domain = $this->eavFactory->createDomain();
        $set = $this->eavFactory->createAttributeSet();
        $group = $this->eavFactory->createGroup($set);
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
            ]
        ];

        $this->eavFactory->createEavEntity($fields, $domain, $set);

        $this->manager->setDomainKey($domain->getKey());
        $this->manager->setSetKey($set->getKey());
        $attributes = $this->manager->getStoredAttributes();
        $this->assertEquals(2, $attributes->count());
        /** @var AttributeModel $attr1 */
        $attr1 = $attributes[0];
        /** @var AttributeModel $attr2 */
        $attr2 = $attributes[1];
        $this->assertEquals('string', $attr1->getName());
        $this->assertEquals($domain->getKey(), $attr1->getDomainKey());
        $this->assertEquals($set->getKey(), $attr1->{_PIVOT::SET_ID->column()});
        $this->assertEquals($group->getKey(), $attr1->{_PIVOT::GROUP_ID->column()});

        $this->assertEquals('integer', $attr2->getName());
        $this->assertEquals($domain->getKey(), $attr2->getDomainKey());
        $this->assertEquals($set->getKey(), $attr2->{_PIVOT::SET_ID->column()});
        $this->assertEquals($group->getKey(), $attr2->{_PIVOT::GROUP_ID->column()});
    }
}