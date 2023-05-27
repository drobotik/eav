<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueSet;

use Drobotik\Eav\Import\Content\Value;
use Drobotik\Eav\Import\Content\ValueSet;
use PHPUnit\Framework\TestCase;

class ValueSetFunctionalTest extends TestCase
{
    private ValueSet $valueSet;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueSet = new ValueSet();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\ValueSet::appendValue
     * @covers \Drobotik\Eav\Import\Content\ValueSet::getValues
     */
    public function values()
    {
        $value = new Value();
        $this->valueSet->appendValue($value);
        $this->assertEquals([$value], $this->valueSet->getValues());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\ValueSet::resetValues
     */
    public function reset()
    {
        $value = new Value();
        $this->valueSet->appendValue($value);
        $this->valueSet->resetValues();
        $this->assertEquals([], $this->valueSet->getValues());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\ValueSet::forNewEntities
     */
    public function for_new_entities()
    {
        $value1 = new Value();
        $value2 = new Value();
        $value3 = new Value();
        $value3->setLineIndex(123);
        $this->valueSet->appendValue($value1);
        $this->valueSet->appendValue($value2);
        $this->valueSet->appendValue($value3);

        $this->assertSame([$value3], $this->valueSet->forNewEntities());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\ValueSet::forExistingEntities
     */
    public function for_existing_entities()
    {
        $value1 = new Value();
        $value1->setEntityKey(123);
        $value2 = new Value();
        $value2->setEntityKey(123);
        $value3 = new Value();
        $this->valueSet->appendValue($value1);
        $this->valueSet->appendValue($value2);
        $this->valueSet->appendValue($value3);

        $this->assertSame([$value1, $value2], $this->valueSet->forExistingEntities());
    }
}