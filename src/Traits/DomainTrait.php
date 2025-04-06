<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Traits;

use Kuperwood\Eav\Domain;

trait DomainTrait
{
    private Domain $domain;
    public function setDomain(Domain $domain): void
    {
        $this->domain = $domain;
    }

    public function getDomain() : Domain
    {
        return $this->domain;
    }

}