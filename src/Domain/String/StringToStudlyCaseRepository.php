<?php

namespace Omatech\Hexagon\Domain\String;

interface StringToStudlyCaseRepository
{
    public function execute(string $text): string;
}