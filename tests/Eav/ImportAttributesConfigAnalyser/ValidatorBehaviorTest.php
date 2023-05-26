<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigAnalyser;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Import\Attributes\Validator;
use Drobotik\Eav\Import\ImportContainer;
use PHPUnit\Framework\TestCase;

class ValidatorBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Validator::validatePivots
     */
    public function validate()
    {
        $columns = [123];
        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['getHeader'])->getMock();
        $driver->expects($this->once())->method('getHeader')
            ->willReturn($columns);
        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDriver'])->getMock();
        $container->expects($this->once())->method('getDriver')->willReturn($driver);
        $validator = $this->getMockBuilder(Validator::class)
            ->onlyMethods([
                'getContainer',
                'fetchStoredAttributes',
                'analyse',
                'validateAttributes'
            ])->getMock();
        $validator->expects($this->once())->method('getContainer')->willReturn($container);
        $validator->expects($this->once())->method('fetchStoredAttributes');
        $validator->expects($this->once())->method('analyse')->with($columns);
        $validator->expects($this->once())->method('validateAttributes');
        $validator->validate();
    }
}