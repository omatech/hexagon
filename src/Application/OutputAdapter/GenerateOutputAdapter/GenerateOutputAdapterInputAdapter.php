<?php

namespace Omatech\Hexagon\Application\OutputAdapter\GenerateOutputAdapter;

final class GenerateOutputAdapterInputAdapter
{
    /** @var string */
    public $domain;
    /** @var string */
    public $useCase;
    /** @var bool */
    private $overwrite;

    public function __construct(string $domain, string $useCase, bool $overwrite)
    {
        $this->domain = $domain;
        $this->useCase = $useCase;
        $this->overwrite = $overwrite;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getUseCase(): string
    {
        return $this->useCase;
    }

    public function isOverwrite(): bool
    {
        return $this->overwrite;
    }
}
