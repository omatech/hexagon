<?php

namespace Omatech\Hexagon\Domain\Template;

interface InstantiateRepository
{
    public function execute(string $template, string $path, string $file): bool;
}
