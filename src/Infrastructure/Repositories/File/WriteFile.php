<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\File;

use Omatech\Hexagon\Domain\File\WriteFileRepository;

class WriteFile implements WriteFileRepository
{
    public function execute(string $path, string $fileName, string $content): bool
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $path = rtrim($path, '/') . '/';
        $path = $path . $fileName;

        return (bool) file_put_contents($path, $content);
    }
}