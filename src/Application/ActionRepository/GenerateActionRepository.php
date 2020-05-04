<?php

namespace Omatech\Hexagon\Application\ActionRepository;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;

final class GenerateActionRepository
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateActionRepositoryInputAdapter $request): GenerateActionRepositoryOutputAdapter
    {
        $domain = $this->studlyNames($request->getDomain());
        $action = $this->studlyNames($request->getAction());

        $path = config('hexagon.directories.domain', 'app/Domain/');
        $path = rtrim($path, '/') . '/';
        $path = base_path($path) . $domain;

        $file = $action . 'Repository.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateActionRepositoryOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('action-repository');

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('Action', $action, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateActionRepositoryOutputAdapter::ofError($e->getMessage());
        }

        return GenerateActionRepositoryOutputAdapter::ofSuccess();
    }
}
