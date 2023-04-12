<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Exception;

use Exception;

class EntityException extends Exception
{
    public const UNDEFINED_DOMAIN_KEY = 'Undefined domain key';
    public const UNDEFINED_ATTRIBUTE_SET_KEY = 'Undefined attribute set key';
    public const DOMAIN_NOT_FOUND = 'Domain not found';
    public const ATTR_SET_NOT_FOUND = 'Attribute set not found';
    public const ENTITY_NOT_FOUND = 'Entity not found';

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
    public static function entityNotFound() {
        throw new static(self::ENTITY_NOT_FOUND);
    }
}