<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\Template;

use Omatech\Hexagon\Domain\Template\Interfaces\ReplaceRepository;

class Replace implements ReplaceRepository
{
    public function execute(string $variable, string $value, string $template, $caps = true): string
    {
        if ($caps) {
            $value = ucfirst($value);
        }

        return str_replace('${' . $variable . '}', $value, $template);
    }
}
