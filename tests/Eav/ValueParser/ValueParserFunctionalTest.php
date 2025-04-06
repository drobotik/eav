<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueParser;

use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Value\ValueParser as Parser;
use Tests\TestCase;

class ValueParserFunctionalTest extends TestCase
{
    private Parser $instance;

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new Parser();
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueParser::parse
     */
    public function parse()
    {
        $this->assertEquals(123, $this->instance->parse(ATTR_TYPE::DECIMAL, 123));

        $random = $this->faker->randomFloat(6);
        $this->assertEquals(rtrim(rtrim((string) $random, '0'), '.'), $this->instance->parse(ATTR_TYPE::DECIMAL, $random));
        $this->assertEquals('test', $this->instance->parse(ATTR_TYPE::STRING, 'test'));
        $this->assertEquals('2023-05-20 14:38:12', $this->instance->parse(ATTR_TYPE::DATETIME, '2023-05-20T14:38:12.795974Z'));
        $this->assertEquals('test', $this->instance->parse(ATTR_TYPE::INTEGER, 'test'));
        $this->assertEquals('test', $this->instance->parse(ATTR_TYPE::TEXT, 'test'));
    }
}