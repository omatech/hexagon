<?php

namespace Omatech\Hexagon\Application\Controller;

final class GenerateControllerInputAdapter
{
    /** @var string */
    public $domain;
    /** @var string */
    public $useCase;
    /** @var bool */
    private $overwrite;
    /** @var string */
    private $type;

    public function __construct(string $domain, string $useCase, bool $overwrite, string $type)
    {
        $this->domain = $domain;
        $this->useCase = $useCase;
        $this->overwrite = $overwrite;
        $this->type = $type;
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

    public function getType(): string
    {
        return $this->type;
    }
}
