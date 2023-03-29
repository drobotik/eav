<?php

namespace Tests\Unit;

use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private Result $result;
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new Result();
    }

    /** @test */
    public function code()
    {
        $result = $this->result->setCode(1);
        $this->assertSame($this->result, $result);
        $this->assertEquals(1, $this->result->getCode());
    }

    /** @test */
    public function message()
    {
        $result = $this->result->setMessage('test');
        $this->assertSame($this->result, $result);
        $this->assertEquals('test', $this->result->getMessage());
    }

    /** @test */
    public function data()
    {
        $this->assertNull($this->result->getData());
        $result = $this->result->setData(['data']);
        $this->assertSame($this->result, $result);
        $this->assertEquals(['data'], $this->result->getData());
    }
}