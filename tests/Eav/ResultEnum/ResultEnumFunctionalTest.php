<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ResultEnum;

use Drobotik\Eav\Enum\_RESULT;
use PHPUnit\Framework\TestCase;

class ResultEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_RESULT::code
     */
    public function codes() {
        $cases = [];
        foreach (_RESULT::cases() as $case) {
            $cases[$case->name] = $case->code();
        }
        $this->assertEquals([
            _RESULT::CREATED->name => 1,
            _RESULT::UPDATED->name => 2,
            _RESULT::FOUND->name => 3,
            _RESULT::NOT_FOUND->name => 4,
            _RESULT::NOT_ENOUGH_ARGS->name => 5,
            _RESULT::NOT_ALLOWED->name => 6,
            _RESULT::EMPTY->name => 7,
            _RESULT::DELETED->name => 8,
            _RESULT::NOT_DELETED->name => 9,
            _RESULT::VALIDATION_FAILS->name => 10,
            _RESULT::VALIDATION_PASSED->name => 11,
            _RESULT::EXPORT_SUCCESS->name => 12,
            _RESULT::EXPORT_FAILED->name => 13,
            _RESULT::IMPORT_SUCCESS->name => 14,
            _RESULT::IMPORT_FAILED->name => 15,
        ], $cases);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_RESULT::message
     */
    public function messages() {
        $cases = [];
        foreach (_RESULT::cases() as $case) {
            $cases[$case->name] = $case->message();
        }
        $this->assertEquals([
            _RESULT::CREATED->name => 'Created',
            _RESULT::UPDATED->name => 'Updated',
            _RESULT::FOUND->name => 'Found',
            _RESULT::NOT_FOUND->name => 'Not found',
            _RESULT::NOT_ENOUGH_ARGS->name => 'Not enough arguments',
            _RESULT::NOT_ALLOWED->name => 'Not allowed',
            _RESULT::EMPTY->name => 'Nothing to perform',
            _RESULT::DELETED->name => 'Deleted',
            _RESULT::NOT_DELETED->name => 'Not deleted',
            _RESULT::VALIDATION_FAILS->name => 'Validation fails',
            _RESULT::VALIDATION_PASSED->name => 'Validation passed',
            _RESULT::EXPORT_SUCCESS->name => 'Successfully exported',
            _RESULT::EXPORT_FAILED->name => 'Export failed',
            _RESULT::IMPORT_SUCCESS->name => 'Successfully imported',
            _RESULT::IMPORT_FAILED->name => 'Import failed',
        ], $cases);
    }
}
