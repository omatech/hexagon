<?php

namespace Omatech\Hexagon\Application\Action;

final class GenerateActionInputAdapter
{
    /** @var string */
    public $domain;
    /** @var string */
    public $action;
    /** @var bool */
    private $overwrite;

    public function __construct(string $domain, string $action, bool $overwrite)
    {
        $this->domain = $domain;
        $this->action = $action;
        $this->overwrite = $overwrite;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function isOverwrite(): bool
    {
        return $this->overwrite;
    }
}
