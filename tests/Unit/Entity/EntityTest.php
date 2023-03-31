<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Tests\TestCase;

class EntityTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }

    /** @test */
    public function key() {
        $this->assertNull($this->entity->getKey());
        $this->entity->setKey(1);
        $this->assertEquals(1, $this->entity->getKey());
        $this->entity->setKey(null);
        $this->assertNull($this->entity->getKey());
    }

    /** @test */
    public function domain_key() {
        $this->assertNull($this->entity->getDomainKey());
        $this->entity->setDomainKey(1);
        $this->assertEquals(1, $this->entity->getDomainKey());
        $this->entity->setDomainKey(null);
        $this->assertNull($this->entity->getDomainKey());
    }
}