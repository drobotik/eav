<?php

namespace Tests\Unit\Value;

use Kuperwood\Eav\Enum\VALUE_RESULT;
use PHPUnit\Framework\TestCase;
use Kuperwood\Eav\Result\ValueResult;

class ValueResultTest extends TestCase
{
    private ValueResult $result;
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new ValueResult();
    }

    /** @test */
    public function created()
    {
        $this->result->created();
        $this->assertEquals(VALUE_RESULT::CREATED->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::CREATED->message(), $this->result->getMessage());
    }

    /** @test */
    public function updated()
    {
        $this->result->updated();
        $this->assertEquals(VALUE_RESULT::UPDATED->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::UPDATED->message(), $this->result->getMessage());
    }

    /** @test */
    public function deleted()
    {
        $this->result->deleted();
        $this->assertEquals(VALUE_RESULT::DELETED->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::DELETED->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_deleted()
    {
        $this->result->notDeleted();
        $this->assertEquals(VALUE_RESULT::NOT_DELETED->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::NOT_DELETED->message(), $this->result->getMessage());
    }

    /** @test */
    public function found()
    {
        $this->result->found();
        $this->assertEquals(VALUE_RESULT::FOUND->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::FOUND->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_found()
    {
        $this->result->notFound();
        $this->assertEquals(VALUE_RESULT::NOT_FOUND->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::NOT_FOUND->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_enough_args()
    {
        $this->result->notEnoughArgs();
        $this->assertEquals(VALUE_RESULT::NOT_ENOUGH_ARGS->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::NOT_ENOUGH_ARGS->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_allowed()
    {
        $this->result->notAllowed();
        $this->assertEquals(VALUE_RESULT::NOT_ALLOWED->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::NOT_ALLOWED->message(), $this->result->getMessage());
    }

    /** @test */
    public function empty()
    {
        $this->result->empty();
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $this->result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $this->result->getMessage());
    }
}