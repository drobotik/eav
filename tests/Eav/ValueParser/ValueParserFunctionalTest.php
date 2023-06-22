<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueParser;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Value\ValueParser;
use Tests\TestCase;

class ValueParserFunctionalTest extends TestCase
{
    private ValueParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = new ValueParser();
    }

    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Value\ValueParser::parse
     */
    public function parse()
    {
        $parser = $this->getMockBuilder(ValueParser::class)
            ->onlyMethods(['parseDecimal'])->getMock();

        $parser->expects($this->once())
            ->method('parseDecimal')
            ->with(123)
            ->willReturn(456);

        $this->assertEquals(456, $parser->parse(ATTR_TYPE::DECIMAL, 123));
        $this->assertEquals('test', $parser->parse(ATTR_TYPE::STRING, 'test'));
        $this->assertEquals('test', $parser->parse(ATTR_TYPE::DATETIME, 'test'));
        $this->assertEquals('test', $parser->parse(ATTR_TYPE::INTEGER, 'test'));
        $this->assertEquals('test', $parser->parse(ATTR_TYPE::TEXT, 'test'));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueParser::parseDecimal
     */
    public function parse_decimal()
    {
        $value = 627622.1833178335;
        $expected = 627622.183318;
        $result = $this->parser->parseDecimal($value);
        $this->assertEquals($expected, $result);
    }


    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueParser::parseDecimal
     */
    public function parse_decimal2()
    {
        $value = 4374530.552935;
        $expected = 4374530.552935;
        $result = $this->parser->parseDecimal($value);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueParser::parseDecimal
     */
    public function parse_decimal3()
    {
        $value = 181903275.402301;
        $expected = 181903275.402301;
        $result = $this->parser->parseDecimal($value);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueParser::parseDecimal
     */
    public function parse_decimal4()
    {
        $value = '181903275.402301';
        $expected = 181903275.402301;
        $result = $this->parser->parseDecimal($value);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueParser::parseDecimal
     */
    public function parse_decimal_spontaneous_value()
    {
        $value = $this->faker->randomFloat(ATTR_TYPE::DECIMAL->migrateOptions()['scale']);
        $result = $this->parser->parseDecimal($value);
        $this->assertEquals($value, $result);
    }
}