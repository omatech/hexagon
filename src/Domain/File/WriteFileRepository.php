<?php

namespace Omatech\Hexagon\Domain\File;

interface WriteFileRepository
{
    public function execute(string $path, string $fileName, string $content): bool;
}