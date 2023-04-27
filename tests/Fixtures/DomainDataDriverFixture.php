<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Fixtures;

use Drobotik\Eav\DomainDataDriver;
use Drobotik\Eav\Result\Result;

class DomainDataDriverFixture extends DomainDataDriver
{
    private ?Result $result = null;

    public function run(): Result
    {
        return $this->hasResult()
            ? $this->getResult()
            : new Result();
    }

    public function setResult(Result $result)
    {
        $this->result = $result;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function hasResult(): bool
    {
        return !is_null($this->result);
    }
}
