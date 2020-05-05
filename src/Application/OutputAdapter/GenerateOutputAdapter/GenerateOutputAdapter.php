<?php

namespace Omatech\Hexagon\Application\OutputAdapter\GenerateOutputAdapter;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\GetRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\ReplaceRepository;

final class GenerateOutputAdapter
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var ReplaceRepository */
    private $replaceRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var StringToStudlyCaseRepository */
    private $stringToStudlyCaseRepository;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        ReplaceRepository $replaceRepository,
        GetRepository $getRepository,
        StringToStudlyCaseRepository $stringToStudlyCaseRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
        $this->replaceRepository = $replaceRepository;
        $this->getRepository = $getRepository;
        $this->stringToStudlyCaseRepository = $stringToStudlyCaseRepository;
    }

    public function execute(GenerateOutputAdapterInputAdapter $inputAdapter): GenerateOutputAdapterOutputAdapter
    {
        try {
            $domain = $this->stringToStudlyCaseRepository->execute($inputAdapter->getDomain());
            $useCase = $this->stringToStudlyCaseRepository->execute($inputAdapter->getUseCase());

            $applicationPath = config('hexagon.directories.application', 'app/Application/');
            $applicationPath = rtrim($applicationPath, '/') . '/';
            $folder = config('hexagon.directories.output-adapter.folder', '');

            if (!empty($folder)) {
                $folder =  trim($folder, '/');
            }

            $name = config('hexagon.directories.output-adapter.name', 'OutputAdapter') ?? 'OutputAdapter';

            $path = base_path($applicationPath) . $domain . '/' . $useCase . '/' . $folder;

            $file =  $useCase . $name . '.php';

            if (file_exists($path . '/' . $file) && !$inputAdapter->isOverwrite()) {
                return GenerateOutputAdapterOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
            }

            $template = $this->getRepository->execute('output-adapter');

            if (!empty($folder)) {
                $folder = '\\' . $folder;
            }

            $template = $this->replaceRepository->execute('Domain', $domain, $template);
            $template = $this->replaceRepository->execute('UseCase', $useCase, $template);
            $template = $this->replaceRepository->execute('useCase', lcfirst($useCase), $template);
            $template = $this->replaceRepository->execute('Folder', $folder ?? '', $template);
            $template = $this->replaceRepository->execute('Name', $name, $template);
    //        $template = $this->clearTemplate($template);

            $this->instantiateRepository->execute($template, $path, $file);

        } catch (\Exception $e) {
            return GenerateOutputAdapterOutputAdapter::ofError($e->getMessage());
        }

        return GenerateOutputAdapterOutputAdapter::ofSuccess();
    }
}
