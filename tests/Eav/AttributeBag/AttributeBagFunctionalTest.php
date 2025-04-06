<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeBag;

use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeBagFunctionalTest extends TestCase
{
    protected AttributeBag $bag;

    public function setUp(): void
    {
        parent::setUp();
        $this->bag = new AttributeBag();
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeBag::setField
     * @covers \Kuperwood\Eav\AttributeBag::getField
     * @covers \Kuperwood\Eav\AttributeBag::resetField
     */
    public function field() {
        $this->bag->setField(_ATTR::STRATEGY, 'test');
        $this->assertEquals('test', $this->bag->getField(_ATTR::STRATEGY));
        $this->bag->resetField(_ATTR::STRATEGY);
        $this->assertEquals(Strategy::class, $this->bag->getField(_ATTR::STRATEGY));
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeBag::getFields
     * @covers \Kuperwood\Eav\AttributeBag::__construct
     */
    public function get_fields() {
        $this->assertSame(_ATTR::bag(), $this->bag->getFields());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeBag::setFields
     * @covers \Kuperwood\Eav\AttributeBag::getFields
     */
    public function set_fields() {
        $input = [
            _ATTR::ID => 123,
            _ATTR::STRATEGY => 'test',
        ];
        $result = $this->bag->setFields($input);
        $this->assertSame($this->bag, $result);
        $this->assertEquals($input, $this->bag->getFields());
    }
}