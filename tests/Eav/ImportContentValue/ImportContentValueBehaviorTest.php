<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContentValue;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\Value;
use Drobotik\Eav\Value\ValueParser;
use PHPUnit\Framework\TestCase;

class ImportContentValueBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setValue
     */
    public function value()
    {
        $type = ATTR_TYPE::TEXT;
        $value = 'test';
        $result = 'testtest';

        $parser = $this->getMockBuilder(ValueParser::class)
            ->onlyMethods(['parse'])
            ->getMock();
        $parser->expects($this->once())->method('parse')->with($type, $value)->willReturn($result);

        $configValue = $this->getMockBuilder(Value::class)
            ->onlyMethods(['makeValueParser'])->getMock();
        $configValue->expects($this->once())->method('makeValueParser')->willReturn($parser);
        $configValue->setType($type);
        $configValue->setValue($value);
        $this->assertEquals($configValue->getValue(), $result);
    }

}