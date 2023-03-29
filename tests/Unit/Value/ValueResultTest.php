<?php

namespace Tests\Unit\Value;

use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class ValueResultTest extends TestCase
{
    private Result $result;
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new Result();
    }

    /** @test */
    public function created()
    {
        $this->result->created();
        $this->assertEquals(_RESULT::CREATED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $this->result->getMessage());
    }

    /** @test */
    public function updated()
    {
        $this->result->updated();
        $this->assertEquals(_RESULT::UPDATED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $this->result->getMessage());
    }

    /** @test */
    public function deleted()
    {
        $this->result->deleted();
        $this->assertEquals(_RESULT::DELETED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_deleted()
    {
        $this->result->notDeleted();
        $this->assertEquals(_RESULT::NOT_DELETED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_DELETED->message(), $this->result->getMessage());
    }

    /** @test */
    public function found()
    {
        $this->result->found();
        $this->assertEquals(_RESULT::FOUND->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_found()
    {
        $this->result->notFound();
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_enough_args()
    {
        $this->result->notEnoughArgs();
        $this->assertEquals(_RESULT::NOT_ENOUGH_ARGS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_ENOUGH_ARGS->message(), $this->result->getMessage());
    }

    /** @test */
    public function not_allowed()
    {
        $this->result->notAllowed();
        $this->assertEquals(_RESULT::NOT_ALLOWED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_ALLOWED->message(), $this->result->getMessage());
    }

    /** @test */
    public function empty()
    {
        $this->result->empty();
        $this->assertEquals(_RESULT::EMPTY->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $this->result->getMessage());
    }

    /** @test */
    public function validation_fails()
    {
        $this->result->validationFails();
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $this->result->getMessage());
    }

    /** @test */
    public function validation_passed()
    {
        $this->result->validationPassed();
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $this->result->getMessage());
    }
}