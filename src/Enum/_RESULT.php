<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Enum;

class _RESULT
{
    const CREATED = 1;
    const UPDATED = 2;
    const FOUND = 3;
    const NOT_FOUND = 4;
    const NOT_ENOUGH_ARGS = 5;
    const NOT_ALLOWED = 6;
    const EMPTY = 7;
    const DELETED = 8;
    const NOT_DELETED = 9;
    const VALIDATION_FAILS = 10;
    const VALIDATION_PASSED = 11;
    const EXPORT_SUCCESS = 12;
    const EXPORT_FAILED = 13;
    const IMPORT_SUCCESS = 14;
    const IMPORT_FAILED = 15;

    public static function code($result)
    {
        $validResults = [
            self::CREATED, self::UPDATED, self::FOUND, self::NOT_FOUND,
            self::NOT_ENOUGH_ARGS, self::NOT_ALLOWED, self::EMPTY, self::DELETED,
            self::NOT_DELETED, self::VALIDATION_FAILS, self::VALIDATION_PASSED,
            self::EXPORT_SUCCESS, self::EXPORT_FAILED, self::IMPORT_SUCCESS, self::IMPORT_FAILED
        ];

        return in_array($result, $validResults, true) ? $result : null;
    }

    public static function message($result)
    {
        $messages = [
            self::CREATED => 'Created',
            self::UPDATED => 'Updated',
            self::FOUND => 'Found',
            self::NOT_FOUND => 'Not found',
            self::NOT_ENOUGH_ARGS => 'Not enough arguments',
            self::NOT_ALLOWED => 'Not allowed',
            self::EMPTY => 'Nothing to perform',
            self::DELETED => 'Deleted',
            self::NOT_DELETED => 'Not deleted',
            self::VALIDATION_FAILS => 'Validation fails',
            self::VALIDATION_PASSED => 'Validation passed',
            self::EXPORT_SUCCESS => 'Successfully exported',
            self::EXPORT_FAILED => 'Export failed',
            self::IMPORT_SUCCESS => 'Successfully imported',
            self::IMPORT_FAILED => 'Import failed',
        ];

        return $messages[$result] ?? 'Unknown result';
    }
}
