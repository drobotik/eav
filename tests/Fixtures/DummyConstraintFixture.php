<?php

namespace Tests\Fixtures;

use Drobotik\Eav\Interfaces\ConstraintInterface;

class DummyConstraintFixture implements ConstraintInterface
{
    private $shouldFail;

    public function __construct(bool $shouldFail = false)
    {
        $this->shouldFail = $shouldFail;
    }

    public function validate($value): ?string
    {
        return $this->shouldFail ? "Invalid value" : null;
    }
}