<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\Domain;

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