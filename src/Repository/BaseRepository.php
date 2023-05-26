<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Factory\EavFactory;

class BaseRepository
{
    protected EavFactory $factory;
    public function __construct()
    {
        $this->factory = new EavFactory();
    }

    public function getFactory(): EavFactory
    {
        return $this->factory;
    }
}