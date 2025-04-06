<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DomainEnum;

use Kuperwood\Eav\Enum\_DOMAIN;
use PHPUnit\Framework\TestCase;

class DomainEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_DOMAIN::table
     */
    public function table() {
        $this->assertEquals('eav_domains', _DOMAIN::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_DOMAIN::ID
     * @covers \Kuperwood\Eav\Enum\_DOMAIN::NAME
     */
    public function columns() {
        $this->assertEquals('domain_id', _DOMAIN::ID);
        $this->assertEquals('name', _DOMAIN::NAME);
    }

}
