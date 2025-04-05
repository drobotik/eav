<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interfaces\ConstraintInterface;

class NotBlankConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if (trim($value) === '') {
            return "This value cannot be blank.";
        }
        return null;
    }
}