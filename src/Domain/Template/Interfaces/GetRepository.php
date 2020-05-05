<?php

namespace Omatech\Hexagon\Domain\Template\Interfaces;

interface GetRepository
{
    public function execute(string $template): string;
}