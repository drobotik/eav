<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import\Attributes;

use Drobotik\Eav\Exception\ImportException;

class ConfigValidator
{
    private Analyzes $analyzes;
    private Config   $config;

    public function setAnalyzes(Analyzes $analyserResult): void
    {
        $this->analyzes = $analyserResult;
    }

    public function getAnalyzes() : Analyzes
    {
        return $this->analyzes;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    /**
     * @throws ImportException
     */
    public function validate(): void
    {
        $this->validateAttributes();
        $this->validatePivots();
    }

    /**
     * @throws ImportException
     */
    public function validateAttributes() : bool
    {
        $analyzes = $this->getAnalyzes();
        $config = $this->getConfig();
        $output = [];
        foreach($analyzes->getAttributes() as $name)
        {
            if(!$config->hasAttribute($name))
            {
                $output[] = $name;
            }
        }

        if(count($output) > 0)
        {
            ImportException::configMissedAttributes($output);
        }

        return true;
    }

    /**
     * @throws ImportException
     */
    public function validatePivots() : bool
    {
        $analyzes = $this->getAnalyzes();
        $config = $this->getConfig();
        $output = [];
        foreach($analyzes->getPivots() as $key => $name)
        {
            if(!$config->hasPivot($key))
            {
                $output[] = $name;
            }
        }

        if(count($output) > 0)
        {
            ImportException::configMissedPivots($output);
        }

        return true;
    }
}