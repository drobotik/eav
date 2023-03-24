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
        $this->result->setCode(1);
        $this->assertEquals(1, $this->result->getCode());
    }

    /** @test */
    public function message()
    {
        $this->result->setMessage('test');
        $this->assertEquals('test', $this->result->getMessage());
    }
}