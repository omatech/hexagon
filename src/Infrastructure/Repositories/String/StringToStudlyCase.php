<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\String;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;

class StringToStudlyCase implements StringToStudlyCaseRepository
{
    public function execute(string $text): string
    {
        $text = preg_replace(["/[^a-zA-Z\d]/", '/__+/'], ' ', $text);
        $text = ucwords($text);
        $text = str_replace(' ', '', $text);

        return Str::studly($text);
    }
}