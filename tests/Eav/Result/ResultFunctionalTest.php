<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Result;

use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Result\Result;
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
     * @covers \Kuperwood\Eav\Result\Result::getCode
     * @covers \Kuperwood\Eav\Result\Result::setCode
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
     * @covers \Kuperwood\Eav\Result\Result::getMessage
     * @covers \Kuperwood\Eav\Result\Result::setMessage
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
     * @covers \Kuperwood\Eav\Result\Result::getData
     * @covers \Kuperwood\Eav\Result\Result::setData
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
     * @covers \Kuperwood\Eav\Result\Result::created
     */
    public function created()
    {
        $this->result->created();
        $this->assertEquals(_RESULT::CREATED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::updated
     */
    public function updated()
    {
        $this->result->updated();
        $this->assertEquals(_RESULT::UPDATED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::UPDATED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::deleted
     */
    public function deleted()
    {
        $this->result->deleted();
        $this->assertEquals(_RESULT::DELETED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::DELETED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::notDeleted
     */
    public function not_deleted()
    {
        $this->result->notDeleted();
        $this->assertEquals(_RESULT::NOT_DELETED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_DELETED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::found
     */
    public function found()
    {
        $this->result->found();
        $this->assertEquals(_RESULT::FOUND, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::FOUND), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::notFound
     */
    public function not_found()
    {
        $this->result->notFound();
        $this->assertEquals(_RESULT::NOT_FOUND, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_FOUND), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::notEnoughArgs
     */
    public function not_enough_args()
    {
        $this->result->notEnoughArgs();
        $this->assertEquals(_RESULT::NOT_ENOUGH_ARGS, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_ENOUGH_ARGS), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::notAllowed
     */
    public function not_allowed()
    {
        $this->result->notAllowed();
        $this->assertEquals(_RESULT::NOT_ALLOWED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_ALLOWED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::empty
     */
    public function empty()
    {
        $this->result->empty();
        $this->assertEquals(_RESULT::EMPTY, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::validationFails
     */
    public function validation_fails()
    {
        $this->result->validationFails();
        $this->assertEquals(_RESULT::VALIDATION_FAILS, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::VALIDATION_FAILS), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::validationPassed
     */
    public function validation_passed()
    {
        $this->result->validationPassed();
        $this->assertEquals(_RESULT::VALIDATION_PASSED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::VALIDATION_PASSED), $this->result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::exportSuccess
     */
    public function export_success()
    {
        $this->result->exportSuccess();
        $this->assertEquals(_RESULT::EXPORT_SUCCESS, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EXPORT_SUCCESS), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::exportFailed
     */
    public function export_failed()
    {
        $this->result->exportFailed();
        $this->assertEquals(_RESULT::EXPORT_FAILED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EXPORT_FAILED), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::importSuccess
     */
    public function import_success()
    {
        $this->result->importSuccess();
        $this->assertEquals(_RESULT::IMPORT_SUCCESS, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::IMPORT_SUCCESS), $this->result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\Result::importFailed
     */
    public function import_failed()
    {
        $this->result->importFailed();
        $this->assertEquals(_RESULT::IMPORT_FAILED, $this->result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::IMPORT_FAILED), $this->result->getMessage());
    }
}