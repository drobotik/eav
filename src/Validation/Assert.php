<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Validation;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Assert
{
    public static function integer(): Constraints\Callback
    {
        return new Constraints\Callback(function($value, ExecutionContextInterface $context) {
            if (!is_int($value) || $value < 0) {
                $context->buildViolation('The value "{{ value }}" is not a valid integer.')
                    ->setParameter('{{ value }}', (string) $value)
                    ->addViolation();
            }
        });
    }
}