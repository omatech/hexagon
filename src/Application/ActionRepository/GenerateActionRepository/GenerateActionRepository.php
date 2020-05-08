<?php

namespace Omatech\Hexagon\Application\ActionRepository\GenerateActionRepository;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateActionRepository
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var StringToStudlyCaseRepository */
    private $stringToStudlyCaseRepository;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        GetRepository $getRepository,
        StringToStudlyCaseRepository $stringToStudlyCaseRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
        $this->getRepository = $getRepository;
        $this->stringToStudlyCaseRepository = $stringToStudlyCaseRepository;
    }

    public function execute(GenerateActionRepositoryInputAdapter $request): GenerateActionRepositoryOutputAdapter
    {
        $domain = $this->stringToStudlyCaseRepository->execute($request->getDomain());
        $action = $this->stringToStudlyCaseRepository->execute($request->getAction());

        $path = config('hexagon.directories.domain', 'app/Domain/');
        $path = rtrim($path, '/') . '/';
        $path = base_path($path) . $domain;

        $file = $action . 'Repository.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateActionRepositoryOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getRepository->execute('action-repository');

        $template->replace('Domain', $domain);
        $template->replace('Action', $action);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateActionRepositoryOutputAdapter::ofError($e->getMessage());
        }

        return GenerateActionRepositoryOutputAdapter::ofSuccess($action . 'Repository');
    }
}
