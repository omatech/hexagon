<?php

namespace Omatech\Hexagon\Application\ActionRepository\BindRepository;

final class BindRepositoryInputAdapter
{
    /** @var string */
    private $domain;
    /** @var string */
    private $action;
    /** @var string */
    private $repository;
    /** @var string */
    private $boundary;

    public function __construct(string $domain, string $action, string $repository, string $boundary = null)
    {
        $this->domain = $domain;
        $this->action = $action;
        $this->repository = $repository;
        $this->boundary = $boundary;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getBoundary(): ?string
    {
        return $this->boundary;
    }
}