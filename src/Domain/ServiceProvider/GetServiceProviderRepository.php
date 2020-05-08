<?php

namespace Omatech\Hexagon\Domain\ServiceProvider;

interface GetServiceProviderRepository
{
    public function execute(string $name): ServiceProvider;
}