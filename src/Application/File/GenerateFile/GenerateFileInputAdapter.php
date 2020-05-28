<?php

namespace Omatech\Hexagon\Application\File\GenerateFile;

final class GenerateFileInputAdapter
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var string */
    private $layer;
    /** @var string */
    private $domain;
    /** @var bool */
    private $overwrite;
    /** @var string */
    private $boundary;

    public function __construct(string $name, string $type, string $layer, string $domain, bool $overwrite, ?string $boundary)
    {
        $this->domain = $domain;
        $this->overwrite = $overwrite;
        $this->boundary = $boundary;
        $this->type = $type;
        $this->layer = $layer;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLayer(): string
    {
        return $this->layer;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isOverwrite(): bool
    {
        return $this->overwrite;
    }

    public function getBoundary(): ?string
    {
        return $this->boundary;
    }
}
