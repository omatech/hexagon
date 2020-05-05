<?php

namespace Omatech\Hexagon\Application\DomainObject\GenerateDomainObject;

final class GenerateDomainObjectInputAdapter
{
    /** @var string */
    public $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }
}
