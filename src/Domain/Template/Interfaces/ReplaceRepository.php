<?php

namespace Omatech\Hexagon\Domain\Template\Interfaces;

interface ReplaceRepository
{
    public function execute(string $variable, string $value, string $template, $caps = true): string;
}
