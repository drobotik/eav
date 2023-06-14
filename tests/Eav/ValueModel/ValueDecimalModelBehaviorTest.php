<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueModel;

use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Value\ValueParser;
use PHPUnit\Framework\TestCase;

class ValueDecimalModelBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Model\ValueDecimalModel::setValue
     */
    public function set_value()
    {
        $value = 123.34545;

        $parser = $this->getMockBuilder(ValueParser::class)
            ->onlyMethods(['parseDecimal'])->getMock();

        $parser->expects($this->once())->method('parseDecimal')
            ->with($value)
            ->willReturn(123.345);

        $model = $this->getMockBuilder(ValueDecimalModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeValueParser'])->getMock();

        $model->expects($this->once())->method('makeValueParser')->willReturn($parser);

        $model->setValue($value);
    }
}