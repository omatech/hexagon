<?php

namespace Omatech\Hexagon\Domain\Template;

interface GetRepository
{
    public function execute(string $template): Template;
}