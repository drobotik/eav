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
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use PHPUnit\Framework\TestCase;

class ConfigFunctionalTest extends TestCase
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
        $attribute->setFields([_ATTR::NAME->column() => 'test', _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()]);
        $config->appendAttribute($attribute);

        $this->assertSame(['test' => $attribute], $config->getAttributes());
        $this->assertTrue($config->hasAttribute('test'));
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\Config::appendAttribute
     */
    public function attribute_validated_on_append()
    {
        $this->expectException(AttributeException::class);
        $config = new Config();
        $attribute = new ConfigAttribute();
        $attribute->setFields([]);
        $config->appendAttribute($attribute);
    }
}