<?php

namespace Omatech\Hexagon\Domain\ServiceProvider;

class ServiceProvider
{
    /*** @var string */
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function withContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function addBind(string $bind, int $position): void
    {
        $use = rtrim($bind, PHP_EOL) . PHP_EOL;
        $this->content = substr_replace($this->content, $use, $position, 0);
    }

    public function addUse(string $use, int $position): void
    {
        $use = rtrim($use, PHP_EOL) . PHP_EOL;
        $this->content = substr_replace($this->content, $use, $position, 0);
    }

    public function calculateUsePosition(): int
    {
        $namespacePosition = strpos($this->content, 'namespace');
        $usePosition = strpos($this->content, "\n", $namespacePosition);

        return strpos($this->content, "\n", $usePosition + 1) + 1;
    }

    public function calculateBindPosition(): int
    {
        $registerPosition = strpos($this->content, 'public function register');
        $openFunctionPosition = strpos($this->content, '{', $registerPosition);

        return strpos($this->content, PHP_EOL, $openFunctionPosition) + 1;
    }
}