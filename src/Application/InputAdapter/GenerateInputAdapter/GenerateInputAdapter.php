<?php

namespace Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\UseCase\Exception\UseCaseCouldNotBeInstantiated;

final class GenerateInputAdapter
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateInputAdapterInputAdapter $inputAdapter): GenerateInputAdapterOutputAdapter
    {
        try {
            $domain = $this->studlyNames($inputAdapter->getDomain());
            $useCase = $this->studlyNames($inputAdapter->getUseCase());

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

            $template = $this->getTemplate('input-adapter');

            if (!empty($folder)) {
               $folder = '\\' . $folder;
            }

            $template = $this->replace('Domain', $domain, $template);
            $template = $this->replace('UseCase', $useCase, $template);
            $template = $this->replace('useCase', lcfirst($useCase), $template);
            $template = $this->replace('Folder', $folder ?? '', $template);
            $template = $this->replace('Name', $name, $template);
    //        $template = $this->clearTemplate($template);

            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateInputAdapterOutputAdapter::ofError($e->getMessage());
        }

        return GenerateInputAdapterOutputAdapter::ofSuccess();
    }
}
