<?php

namespace Omatech\Hexagon\Application\Action\GenerateAction;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateAction
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

    public function execute(GenerateActionInputAdapter $request): GenerateActionOutputAdapter
    {
        $domain = $this->stringToStudlyCaseRepository->execute($request->getDomain());
        $action = $this->stringToStudlyCaseRepository->execute($request->getAction());

        $actionPath = config('hexagon.directories.action', 'app/Infrastructure/Repositories/');
        $path = base_path($actionPath) . $domain;

        $file = $action . '.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateActionOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getRepository->execute('action');

        $namespaceRoute = str_replace('/', '\\', $actionPath);

        $template->replace('Domain', $domain);
        $template->replace('Action', $action);
        $template->replace('NamespaceRoute', $namespaceRoute);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateActionOutputAdapter::ofError($e->getMessage());
        }

        return GenerateActionOutputAdapter::ofSuccess($action);
    }
}
