<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Result;

use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class ResultFunctionalTest extends TestCase
{
    private Result $result;
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new Result();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::getCode
     * @covers \Drobotik\Eav\Result\Result::setCode
     */
    public function code()
    {
        $result = $this->result->setCode(1);
        $this->assertSame($this->result, $result);
        $this->assertEquals(1, $this->result->getCode());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::getMessage
     * @covers \Drobotik\Eav\Result\Result::setMessage
     */
    public function message()
    {
        $result = $this->result->setMessage('test');
        $this->assertSame($this->result, $result);
        $this->assertEquals('test', $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::getData
     * @covers \Drobotik\Eav\Result\Result::setData
     */
    public function data()
    {
        $this->assertNull($this->result->getData());
        $result = $this->result->setData(['data']);
        $this->assertSame($this->result, $result);
        $this->assertEquals(['data'], $this->result->getData());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::created
     */
    public function created()
    {
        $this->result->created();
        $this->assertEquals(_RESULT::CREATED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::updated
     */
    public function updated()
    {
        $this->result->updated();
        $this->assertEquals(_RESULT::UPDATED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::deleted
     */
    public function deleted()
    {
        $this->result->deleted();
        $this->assertEquals(_RESULT::DELETED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::notDeleted
     */
    public function not_deleted()
    {
        $this->result->notDeleted();
        $this->assertEquals(_RESULT::NOT_DELETED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_DELETED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::found
     */
    public function found()
    {
        $this->result->found();
        $this->assertEquals(_RESULT::FOUND->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::notFound
     */
    public function not_found()
    {
        $this->result->notFound();
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::notEnoughArgs
     */
    public function not_enough_args()
    {
        $this->result->notEnoughArgs();
        $this->assertEquals(_RESULT::NOT_ENOUGH_ARGS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_ENOUGH_ARGS->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::notAllowed
     */
    public function not_allowed()
    {
        $this->result->notAllowed();
        $this->assertEquals(_RESULT::NOT_ALLOWED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::NOT_ALLOWED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::empty
     */
    public function empty()
    {
        $this->result->empty();
        $this->assertEquals(_RESULT::EMPTY->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::validationFails
     */
    public function validation_fails()
    {
        $this->result->validationFails();
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::validationPassed
     */
    public function validation_passed()
    {
        $this->result->validationPassed();
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::exportSuccess
     */
    public function export_success()
    {
        $this->result->exportSuccess();
        $this->assertEquals(_RESULT::EXPORT_SUCCESS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::EXPORT_SUCCESS->message(), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::exportFailed
     */
    public function export_failed()
    {
        $this->result->exportFailed();
        $this->assertEquals(_RESULT::EXPORT_FAILED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::EXPORT_FAILED->message(), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::importSuccess
     */
    public function import_success()
    {
        $this->result->importSuccess();
        $this->assertEquals(_RESULT::IMPORT_SUCCESS->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::IMPORT_SUCCESS->message(), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\Result::importFailed
     */
    public function import_failed()
    {
        $this->result->importFailed();
        $this->assertEquals(_RESULT::IMPORT_FAILED->code(), $this->result->getCode());
        $this->assertEquals(_RESULT::IMPORT_FAILED->message(), $this->result->getMessage());
    }
}