<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Value\ValueParser;

trait SingletonsTrait
{
    public function makeAttributeSetModel() : AttributeSetModel
    {
        return new AttributeSetModel;
    }

    public function makeAttributeContainer() : AttributeContainer
    {
        return new AttributeContainer;
    }

    public function makeEntityModel() : EntityModel
    {
        return new EntityModel();
    }

    public function makeAttributeSet() : AttributeSet
    {
        return new AttributeSet();
    }

    public function makeDomainModel() : DomainModel
    {
        return new DomainModel();
    }

    public function makeEavFactory(): EavFactory
    {
        return new EavFactory();
    }

    public function makeValueParser(): ValueParser
    {
        return new ValueParser();
    }

}