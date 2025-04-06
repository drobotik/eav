<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityBag;

use Kuperwood\Eav\Entity;
use PHPUnit\Framework\TestCase;

class EntityBagFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\EntityBag::getEntity
     * @covers \Kuperwood\Eav\EntityBag::setEntity
     */
    public function entity() {
        $entity = new Entity();
        $bag = $entity->getBag();
        $this->assertSame($entity, $bag->getEntity());
        $entity2 = new Entity();
        $bag->setEntity($entity2);
        $this->assertSame($entity2, $bag->getEntity());
    }
}
