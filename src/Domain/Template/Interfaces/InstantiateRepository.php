<?php

namespace Omatech\Hexagon\Domain\Template\Interfaces;

interface InstantiateRepository
{
    public function execute(string $template, string $path, string $file): bool;
}
