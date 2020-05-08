<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\Template;

use Omatech\Hexagon\Domain\Template\InstantiateRepository;

class Instantiate implements InstantiateRepository
{
    public function execute(string $template, string $path, string $file): bool
    {
        $oldPath = getcwd();

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        chdir($path);
        $put = file_put_contents($file, $template);
        chdir($oldPath);

        return (bool) $put;
    }
}
