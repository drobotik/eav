<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeFactoryEnum;

use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use PHPUnit\Framework\TestCase;

class AttributeFactoryEnumFunctionalTest extends TestCase
{

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_FACTORY::field
     */
    public function columns() {
        $cases = [];
        foreach (ATTR_FACTORY::cases() as $case) {
            $cases[$case->name] = $case->field();
        }
        $this->assertEquals([
            ATTR_FACTORY::ATTRIBUTE->name => 'attribute',
            ATTR_FACTORY::GROUP->name => 'group',
            ATTR_FACTORY::VALUE->name => 'value',
        ], $cases);
    }

}
