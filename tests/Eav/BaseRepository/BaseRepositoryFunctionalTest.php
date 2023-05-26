<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\BaseRepository;

use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Repository\BaseRepository;
use PHPUnit\Framework\TestCase;

class BaseRepositoryFunctionalTest extends TestCase
{

    private BaseRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new BaseRepository();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\BaseRepository::__construct
     * @covers \Drobotik\Eav\Repository\BaseRepository::getFactory
     */
    public function eavFactory()
    {
        $this->assertInstanceOf(EavFactory::class, $this->repository->getFactory());
    }
}