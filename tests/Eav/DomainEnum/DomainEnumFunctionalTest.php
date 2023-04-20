<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DomainEnum;

use Drobotik\Eav\Enum\_DOMAIN;
use PHPUnit\Framework\TestCase;

class DomainEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_DOMAIN::table
     */
    public function table() {
        $this->assertEquals('eav_domains', _DOMAIN::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_DOMAIN::column
     */
    public function columns() {
        $cases = [];
        foreach (_DOMAIN::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _DOMAIN::ID->column() => 'domain_id',
            _DOMAIN::NAME->column() => 'name',
        ], $cases);
    }

}
