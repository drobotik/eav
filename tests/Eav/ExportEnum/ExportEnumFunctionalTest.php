<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportEnum;

use Drobotik\Eav\Enum\EXPORT;
use PHPUnit\Framework\TestCase;

class ExportEnumFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Enum\EXPORT::field
     */
    public function field()
    {
        $this->assertEquals("domain_key", EXPORT::DOMAIN_KEY->field());
        $this->assertEquals("path", EXPORT::PATH->field());
        $this->assertEquals("set_key", EXPORT::SET_KEY->field());
    }
}
