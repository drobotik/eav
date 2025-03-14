<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interface\ConstraintInterface;

class NotBlankConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return "This value cannot be blank.";
        }
        return null;
    }
}