<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigPivot;

use Drobotik\Eav\Import\Attributes\ConfigPivot;
use PHPUnit\Framework\TestCase;

class ConfigPivotFunctionalTest extends TestCase
{
    private ConfigPivot $pivot;

    public function setUp(): void
    {
        parent::setUp();
        $this->pivot = new ConfigPivot();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigPivot::setAttributeKey
     * @covers \Drobotik\Eav\Import\Attributes\ConfigPivot::getAttributeKey
     */
    public function attribute_key()
    {
        $this->pivot->setAttributeKey(123);
        $this->assertEquals(123, $this->pivot->getAttributeKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigPivot::getGroupKey
     * @covers \Drobotik\Eav\Import\Attributes\ConfigPivot::setGroupKey
     */
    public function group_key()
    {
        $this->pivot->setGroupKey(123);
        $this->assertEquals(123, $this->pivot->getGroupKey());
    }
}