<?php

namespace Omatech\Hexagon\Domain\File\Interfaces;

interface InstantiateRepository
{
    public function execute(string $template, string $path, string $file): bool;
}
