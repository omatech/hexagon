<?php

namespace Omatech\Hexagon\Application\ActionRepository\BindRepository;

use Omatech\Hexagon\Domain\File\WriteFileRepository;
use Omatech\Hexagon\Domain\ServiceProvider\GetServiceProviderRepository;
use Omatech\Hexagon\Domain\ServiceProvider\ServiceProvider;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class BindRepository
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var GetServiceProviderRepository */
    private $getServiceProviderRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var WriteFileRepository */
    private $writeFileRepository;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        GetServiceProviderRepository $getServiceProviderRepository,
        GetRepository $getRepository,
        WriteFileRepository $writeFileRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
        $this->getServiceProviderRepository = $getServiceProviderRepository;
        $this->getRepository = $getRepository;
        $this->writeFileRepository = $writeFileRepository;
    }

    public function execute(BindRepositoryInputAdapter $inputAdapter): BindRepositoryOutputAdapter
    {
        // TODO: Bind Repository
        try {
            $serviceProvider = $this->getServiceProviderRepository->execute('repository');
        } catch (\Exception $e) {
            $template = $this->getRepository->execute('service-provider');
            $template->replace('Name', 'Repository');
            $serviceProvider = new ServiceProvider($template->getContent());
        }

        try {
            // Add use for repository and action
            $domainFolder = config('hexagon.directories.domain', 'app/Domain');
            $domainNamespace = str_replace('/', '\\', $domainFolder);
            $domainNamespace = rtrim($domainNamespace, '\\');
            $repositoryNamespace = $domainNamespace . '\\' . $inputAdapter->getDomain();
            $repositoryUse = 'use ' . $repositoryNamespace . '\\'
                . $inputAdapter->getRepository() . ';' . PHP_EOL;

            $infrastructureFolder = config('hexagon.directories.infrastructure', 'app/Infrastructure');
            $infrastructureNamespace = str_replace('/', '\\', $infrastructureFolder);
            $infrastructureNamespace = rtrim($infrastructureNamespace, '\\');
            $actionNamespace = $infrastructureNamespace . '\\Repositories\\' . $inputAdapter->getDomain();
            $actionUse = 'use ' . $actionNamespace . '\\' . $inputAdapter->getAction() . ';' . PHP_EOL;

            $serviceProvider->addUse($actionUse, $serviceProvider->calculateUsePosition());
            $serviceProvider->addUse($repositoryUse, $serviceProvider->calculateUsePosition());

            // Add Repository

            $bind = '        $this->app->bind(' . PHP_EOL;
            $bind .= '            ' . $inputAdapter->getRepository() . '::class,' . PHP_EOL;
            $bind .= '            ' . $inputAdapter->getAction() . '::class' . PHP_EOL;
            $bind .= '         );' . PHP_EOL;

            $serviceProvider->addBind($bind, $serviceProvider->calculateBindPosition());

            $path = config('hexagon.directories.service-providers', 'app/Providers/');
            $fileName = 'RepositoryServiceProvider.php';
            $content = $serviceProvider->getContent();

            $this->writeFileRepository->execute($path, $fileName, $content);

        } catch (\Exception $e) {

            dd($e->getMessage());
            return BindRepositoryOutputAdapter::ofError($e->getMessage());
        }

        return BindRepositoryOutputAdapter::ofSuccess();
    }
}
