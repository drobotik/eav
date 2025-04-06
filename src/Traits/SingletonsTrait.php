<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Traits;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Factory\EavFactory;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Value\ValueParser;
use Faker\Factory;
use Faker\Generator;

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

    public function makeGroupModel() : AttributeGroupModel
    {
        return new AttributeGroupModel();
    }

    public function makePivotModel() : PivotModel
    {
        return new PivotModel();
    }

    public function makeValueModel() : ValueBase
    {
        return new ValueBase();
    }

    public function makeAttributeModel() : AttributeModel
    {
        return new AttributeModel();
    }

    public function makeEavFactory(): EavFactory
    {
        return new EavFactory();
    }

    public function makeValueParser(): ValueParser
    {
        return new ValueParser();
    }

    public function makeFakerGenerator(): Generator
    {
        return Factory::create();
    }

}