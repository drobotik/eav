<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfig;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\ConfigPivot;
use PHPUnit\Framework\TestCase;

class ImportAttributesConfigFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Config::getAttributes
     * @covers \Drobotik\Eav\Import\Attributes\Config::appendAttribute
     * @covers \Drobotik\Eav\Import\Attributes\Config::hasAttribute
     */
    public function attributes()
    {
        $config = new Config();
        $this->assertEquals([], $config->getAttributes());
        $this->assertFalse($config->hasAttribute('test'));

        $attribute = new ConfigAttribute();
        $attribute->setFields([_ATTR::NAME->column() => 'test']);
        $config->appendAttribute($attribute);

        $this->assertSame(['test' => $attribute], $config->getAttributes());
        $this->assertTrue($config->hasAttribute('test'));
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Config::getPivots
     * @covers \Drobotik\Eav\Import\Attributes\Config::appendPivot
     * @covers \Drobotik\Eav\Import\Attributes\Config::hasPivot
     */
    public function pivot()
    {
        $config = new Config();
        $this->assertEquals([], $config->getPivots());
        $this->assertFalse($config->hasPivot(123));

        $pivot = new ConfigPivot();
        $pivot->setAttributeKey(123);
        $config->appendPivot($pivot);

        $this->assertSame([123 => $pivot], $config->getPivots());
        $this->assertTrue($config->hasPivot(123));
    }
}