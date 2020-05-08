<?php

namespace Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateInputAdapter
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

    public function execute(GenerateInputAdapterInputAdapter $inputAdapter): GenerateInputAdapterOutputAdapter
    {
        try {
            $domain = $this->stringToStudlyCaseRepository->execute($inputAdapter->getDomain());
            $useCase = $this->stringToStudlyCaseRepository->execute($inputAdapter->getUseCase());

            $applicationPath = config('hexagon.directories.application', 'app/Application/');
            $applicationPath = rtrim($applicationPath, '/') . '/';
            $folder = config('hexagon.directories.input-adapter.folder', '');

            if (!empty($folder)) {
                $folder =  trim($folder, '/');
            }

            $name = config('hexagon.directories.input-adapter.name', 'InputAdapter') ?? 'InputAdapter';

            $path = base_path($applicationPath) . $domain . '/' . $useCase . '/' . $folder;

            $file =  $useCase . $name . '.php';

            if (file_exists($path . '/' . $file) && !$inputAdapter->isOverwrite()) {
                return GenerateInputAdapterOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
            }

            $template = $this->getRepository->execute('input-adapter');

            if (!empty($folder)) {
               $folder = '\\' . $folder;
            }

            $template->replace('Domain', $domain);
            $template->replace('UseCase', $useCase);
            $template->replace('useCase', lcfirst($useCase));
            $template->replace('Folder', $folder ?? '');
            $template->replace('Name', $name);
    //        $template = $this->clearTemplate($template);

            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateInputAdapterOutputAdapter::ofError($e->getMessage());
        }

        return GenerateInputAdapterOutputAdapter::ofSuccess();
    }
}
