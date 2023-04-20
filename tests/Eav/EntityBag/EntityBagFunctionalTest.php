<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityBag;

use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityBag;
use PHPUnit\Framework\TestCase;

class EntityBagFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityBag::getEntity
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