<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interfaces\ConstraintInterface;

class RequiredConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if ($value === null || $value === '') {
            return "This field is required.";
        }
        return null;
    }
}