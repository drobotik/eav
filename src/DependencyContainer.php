<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Translation\Loader;
class DependencyContainer
{
    private ValidatorFactory $validator;

    public function getValidator() : ValidatorFactory
    {
        return $this->validator;
    }

    public function setValidator(Loader $loader, string $locale = 'en'): self
    {
        $translator = new Translator($loader, $locale);
        $this->validator = new ValidatorFactory($translator);
        return $this;
    }
}