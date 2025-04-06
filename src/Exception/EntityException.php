<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Exception;

use Exception;

class EntityException extends Exception
{
    public const UNDEFINED_DOMAIN_KEY = 'Undefined domain key';
    public const UNDEFINED_ATTRIBUTE_SET_KEY = 'Undefined attribute set key';
    public const DOMAIN_NOT_FOUND = 'Domain not found';
    public const ATTR_SET_NOT_FOUND = 'Attribute set not found';
    public const UNDEFINED_ENTITY_KEY = "Undefined entity key";
    public const ENTITY_NOT_FOUND = 'Entity not found';
    public const MUST_BE_POSITIVE_AMOUNT = 'Must be positive amount';
    public const MUST_BE_ENTITY_KEY = 'Must be entity key';

    /**
     *
     * @throws EntityException
     */
    public static function undefinedDomainKey() {
        throw new static(self::UNDEFINED_DOMAIN_KEY);
    }

    /**
     *
     * @throws EntityException
     */
    public static function domainNotFound() {
        throw new static(self::DOMAIN_NOT_FOUND);
    }

    /**
     *
     * @throws EntityException
     */
    public static function undefinedAttributeSetKey() {
        throw new static(self::UNDEFINED_ATTRIBUTE_SET_KEY);
    }

    /**
     *
     * @throws EntityException
     */
    public static function attrSetNotFound() {
        throw new static(self::ATTR_SET_NOT_FOUND);
    }

    /**
     *
     * @throws EntityException
     */
    public static function undefinedEntityKey() {
        throw new static(self::UNDEFINED_ENTITY_KEY);
    }

    /**
     *
     * @throws EntityException
     */
    public static function entityNotFound() {
        throw new static(self::ENTITY_NOT_FOUND);
    }

    /**
     *
     * @throws EntityException
     */
    public static function mustBePositiveAmount() {
        throw new static(self::MUST_BE_POSITIVE_AMOUNT);
    }

    /**
     *
     * @throws EntityException
     */
    public static function mustBeEntityKey() {
        throw new static(self::MUST_BE_ENTITY_KEY);
    }
}