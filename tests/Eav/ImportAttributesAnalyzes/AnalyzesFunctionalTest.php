<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesAnalyzes;

use Drobotik\Eav\Import\Attributes\Analyzes;
use PHPUnit\Framework\TestCase;

class AnalyzesFunctionalTest extends TestCase
{
    private Analyzes $analyzes;

    public function setUp(): void
    {
        parent::setUp();
        $this->analyzes = new Analyzes();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::appendAttribute
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::getAttributes
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::isAttributes
     */
    public function attributes()
    {
        $this->assertEquals([], $this->analyzes->getAttributes());
        $this->assertFalse($this->analyzes->isAttributes());
        $this->analyzes->appendAttribute('test');
        $this->assertEquals(['test' => 'test'], $this->analyzes->getAttributes());
        $this->assertTrue($this->analyzes->isAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::appendPivot
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::getPivots
     * @covers \Drobotik\Eav\Import\Attributes\Analyzes::isPivots
     */
    public function pivots()
    {
        $this->assertEquals([], $this->analyzes->getPivots());
        $this->assertFalse($this->analyzes->isPivots());
        $this->analyzes->appendPivot(123, 'test');
        $this->assertEquals([123 => 'test'], $this->analyzes->getPivots());
        $this->assertTrue($this->analyzes->isPivots());
    }
}