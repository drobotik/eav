<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ResultEnum;

use Kuperwood\Eav\Enum\_RESULT;
use PHPUnit\Framework\TestCase;

class ResultEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_RESULT::code
     */
    public function codes() {
        $this->assertEquals(1, _RESULT::CREATED);
        $this->assertEquals(2, _RESULT::UPDATED);
        $this->assertEquals(3, _RESULT::FOUND);
        $this->assertEquals(4, _RESULT::NOT_FOUND);
        $this->assertEquals(5, _RESULT::NOT_ENOUGH_ARGS);
        $this->assertEquals(6, _RESULT::NOT_ALLOWED);
        $this->assertEquals(7, _RESULT::EMPTY);
        $this->assertEquals(8, _RESULT::DELETED);
        $this->assertEquals(9, _RESULT::NOT_DELETED);
        $this->assertEquals(10, _RESULT::VALIDATION_FAILS);
        $this->assertEquals(11, _RESULT::VALIDATION_PASSED);
        $this->assertEquals(12, _RESULT::EXPORT_SUCCESS);
        $this->assertEquals(13, _RESULT::EXPORT_FAILED);
        $this->assertEquals(14, _RESULT::IMPORT_SUCCESS);
        $this->assertEquals(15, _RESULT::IMPORT_FAILED);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_RESULT::message
     */
    public function messages() {
        $this->assertEquals('Created', _RESULT::message(_RESULT::CREATED));
        $this->assertEquals('Updated', _RESULT::message(_RESULT::UPDATED));
        $this->assertEquals('Found', _RESULT::message(_RESULT::FOUND));
        $this->assertEquals('Not found', _RESULT::message(_RESULT::NOT_FOUND));
        $this->assertEquals('Not enough arguments', _RESULT::message(_RESULT::NOT_ENOUGH_ARGS));
        $this->assertEquals('Not allowed', _RESULT::message(_RESULT::NOT_ALLOWED));
        $this->assertEquals('Nothing to perform', _RESULT::message(_RESULT::EMPTY));
        $this->assertEquals('Deleted', _RESULT::message(_RESULT::DELETED));
        $this->assertEquals('Not deleted', _RESULT::message(_RESULT::NOT_DELETED));
        $this->assertEquals('Validation fails', _RESULT::message(_RESULT::VALIDATION_FAILS));
        $this->assertEquals('Validation passed', _RESULT::message(_RESULT::VALIDATION_PASSED));
        $this->assertEquals('Successfully exported', _RESULT::message(_RESULT::EXPORT_SUCCESS));
        $this->assertEquals('Export failed', _RESULT::message(_RESULT::EXPORT_FAILED));
        $this->assertEquals('Successfully imported', _RESULT::message(_RESULT::IMPORT_SUCCESS));
        $this->assertEquals('Import failed', _RESULT::message(_RESULT::IMPORT_FAILED));
    }
}
