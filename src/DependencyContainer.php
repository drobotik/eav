<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

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