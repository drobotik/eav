<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigValidator;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Exception\ImportException;
use Drobotik\Eav\Import\Attributes\Analyzes;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\ConfigPivot;
use Drobotik\Eav\Import\Attributes\ConfigValidator;
use PHPUnit\Framework\TestCase;

class ConfigValidatorBehaviorTest extends TestCase
{
    private ConfigValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ConfigValidator();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::setAnalyzes
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::getAnalyzes
     */
    public function analyzes()
    {
        $analyzes = new Analyzes();
        $this->validator->setAnalyzes($analyzes);
        $this->assertSame($analyzes, $this->validator->getAnalyzes());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::setConfig
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::getConfig
     */
    public function config()
    {
        $config = new Config();
        $this->validator->setConfig($config);
        $this->assertSame($config, $this->validator->getConfig());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validateAttributes
     */
    public function validate_attributes()
    {
        $config = new Config();
        $attribute = new ConfigAttribute();
        $attribute->setFields([_ATTR::NAME->column() => 'test 2']);
        $config->appendAttribute($attribute);
        $analyzes = new Analyzes();
        $analyzes->appendAttribute('test 1');
        $analyzes->appendAttribute('test 2');
        $analyzes->appendAttribute('test 3');
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->expectException(ImportException::class);
        $this->expectExceptionMessage(
            sprintf(ImportException::CONFIG_MISSED_ATTRIBUTES, 'test 1,test 3')
        );
        $this->validator->validateAttributes();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validateAttributes
     */
    public function validate_attributes_success()
    {
        $config = new Config();
        $attribute = new ConfigAttribute();
        $attribute->setFields([_ATTR::NAME->column() => 'test 2']);
        $config->appendAttribute($attribute);
        $analyzes = new Analyzes();
        $analyzes->appendAttribute('test 2');
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->assertTrue($this->validator->validateAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validateAttributes
     */
    public function validate_attributes_empty()
    {
        $config = new Config();
        $analyzes = new Analyzes();
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->assertTrue($this->validator->validateAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validatePivots
     */
    public function validate_pivots()
    {
        $config = new Config();
        $pivot = new ConfigPivot();
        $pivot->setAttributeKey(1);
        $config->appendPivot($pivot);
        $analyzes = new Analyzes();
        $analyzes->appendPivot(1, 'test 1');
        $analyzes->appendPivot(2, 'test 2');
        $analyzes->appendPivot(3, 'test 3');
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->expectException(ImportException::class);
        $this->expectExceptionMessage(
            sprintf(ImportException::CONFIG_MISSED_PIVOTS, 'test 2,test 3')
        );
        $this->validator->validatePivots();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validatePivots
     */
    public function validate_pivots_success()
    {
        $config = new Config();
        $pivot = new ConfigPivot();
        $pivot->setAttributeKey(1);
        $config->appendPivot($pivot);
        $analyzes = new Analyzes();
        $analyzes->appendPivot(1, 'test 1');
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->assertTrue($this->validator->validatePivots());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validatePivots
     */
    public function validate_pivots_empty()
    {
        $config = new Config();
        $analyzes = new Analyzes();
        $this->validator->setConfig($config);
        $this->validator->setAnalyzes($analyzes);
        $this->assertTrue($this->validator->validatePivots());
    }
}