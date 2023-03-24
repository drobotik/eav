<?php

namespace Tests\Unit;

use Kuperwood\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function code()
    {
        $result = new Result();
        $result->code = 1;
        $this->assertEquals(1, $result->code());
    }

    public function message()
    {
        $result = new Result();
        $result->message = 'test';
        $this->assertEquals('test', $result->message());
    }
}