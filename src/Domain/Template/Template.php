<?php

namespace Omatech\Hexagon\Domain\Template;

class Template
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

    public function replace(string $variable, string $value, $caps = true): void
    {
        if ($caps) {
            $value = ucfirst($value);
        }

        $this->content = str_replace('${' . $variable . '}', $value, $this->content);
    }
}