<?php

declare(strict_types=1);

namespace Tests\Unit\EntityBag;

use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityBag;
use PHPUnit\Framework\TestCase;

class EntityBagFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers EntityBag::getEntity
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