<?php

namespace Tests\Unit\Value;

use Kuperwood\Eav\Enum\VALUE_RESULT;
use PHPUnit\Framework\TestCase;
use Kuperwood\Eav\Result\ValueResult;

class ValueResultTest extends TestCase
{

    /** @test */
    public function created()
    {
        $result = new ValueResult();
        $result->created();
        $this->assertEquals(VALUE_RESULT::CREATED->code(), $result->code());
        $this->assertEquals(VALUE_RESULT::CREATED->message(), $result->message());
    }

    /** @test */
    public function updated()
    {
        $result = new ValueResult();
        $result->updated();
        $this->assertEquals(VALUE_RESULT::UPDATED->code(), $result->code());
        $this->assertEquals(VALUE_RESULT::UPDATED->message(), $result->message());
    }

    /** @test */
    public function notAllowed()
    {
        $result = new ValueResult();
        $result->notAllowed();
        $this->assertEquals(VALUE_RESULT::NOT_ALLOWED->code(), $result->code());
        $this->assertEquals(VALUE_RESULT::NOT_ALLOWED->message(), $result->message());
    }

    /** @test */
    public function empty()
    {
        $result = new ValueResult();
        $result->empty();
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->code());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->message());
    }
}