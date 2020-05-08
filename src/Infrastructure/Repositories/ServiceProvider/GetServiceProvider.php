<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\ServiceProvider;

use Omatech\Hexagon\Domain\ServiceProvider\GetServiceProviderRepository;
use Omatech\Hexagon\Domain\ServiceProvider\ServiceProvider;
use Omatech\Hexagon\Infrastructure\Exceptions\FileDoesNotExistException;

class GetServiceProvider implements GetServiceProviderRepository
{
    /**  @throws FileDoesNotExistException */
    public function execute(string $name): ServiceProvider
    {
        $path = $this->getServiceProviderPath(ucwords($name));

        return new ServiceProvider(file_get_contents($path));
    }

    /** @throws FileDoesNotExistException */
    protected function getServiceProviderPath(string $serviceProvider): string
    {
        $serviceProvider = preg_replace(["/[^a-zA-Z\d]/", '/__+/'], '', $serviceProvider);
        $serviceProvider = trim($serviceProvider, 'ServiceProvider');

        $serviceProvidersDirectory = 'app/Providers/';

        $path = $serviceProvidersDirectory . $serviceProvider . 'ServiceProvider.php';

        if (!file_exists($path)) {
            throw FileDoesNotExistException::fromException(new \Exception('File not found'));
        }

        return $path;
    }
}