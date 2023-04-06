<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\Model\EntityModel;
use Tests\TestCase;

class EntityModelTest extends TestCase
{

    /** @test */
    public function find_and_delete() {
        $model = new EntityModel();
        $result = $model->findAndDelete(1);
        $this->assertFalse($result);
        $this->eavFactory->createEntity();
        $result = $model->findAndDelete(1);
        $this->assertTrue($result);
    }
}