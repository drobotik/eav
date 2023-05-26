<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportException;

use Drobotik\Eav\Exception\ImportException;
use Tests\TestCase;

class ImportExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\ImportException::configMissedAttributes
     */
    public function config_missed_attributes()
    {
        $attributes = ['Tom','Jerry'];
        $this->expectException(ImportException::class);
        $this->expectExceptionMessage(sprintf(ImportException::CONFIG_MISSED_ATTRIBUTES, implode(',', $attributes)));
        ImportException::configMissedAttributes($attributes);
    }
}