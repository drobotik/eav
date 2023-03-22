<?php

namespace Tests\Unit\Model;


use Kuperwood\Eav\Impl\Doctrine\Model\DomainModel;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    /** @test */
    public function findOne()
    {
        $domain = new DomainModel();
        $domain->setName("test");

        $this->em->persist($domain);
        $this->em->flush();
        $this->assertEquals(1, $domain->getId());
    }

}