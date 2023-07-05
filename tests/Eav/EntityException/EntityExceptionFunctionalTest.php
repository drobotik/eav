<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityException;

use Drobotik\Eav\Exception\EntityException;
use PHPUnit\Framework\TestCase;

class EntityExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::undefinedDomainKey
     */
    public function undefinedDomainKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_DOMAIN_KEY);
        EntityException::undefinedDomainKey();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::domainNotFound
     */
    public function domainNotFound()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        EntityException::domainNotFound();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::undefinedAttributeSetKey
     */
    public function undefinedAttributeSetKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        EntityException::undefinedAttributeSetKey();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::attrSetNotFound
     */
    public function attrSetNotFound()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        EntityException::attrSetNotFound();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::undefinedEntityKey
     */
    public function undefinedEntityKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ENTITY_KEY);
        EntityException::undefinedEntityKey();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::undefinedEntityKey
     */
    public function entityNotFound()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ENTITY_NOT_FOUND);
        EntityException::entityNotFound();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::mustBePositiveAmount
     */
    public function mustBePositiveAmount()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::MUST_BE_POSITIVE_AMOUNT);
        EntityException::mustBePositiveAmount();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityException::mustBeEntityKey
     */
    public function mustBeEntityKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::MUST_BE_ENTITY_KEY);
        EntityException::mustBeEntityKey();
    }
}