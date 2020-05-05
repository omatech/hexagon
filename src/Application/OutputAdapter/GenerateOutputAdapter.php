<?php

namespace Omatech\Hexagon\Application\OutputAdapter;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\UseCase\Exception\UseCaseCouldNotBeInstantiated;

final class GenerateOutputAdapter
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateOutputAdapterInputAdapter $inputAdapter): GenerateOutputAdapterOutputAdapter
    {
        try {
            $domain = $this->studlyNames($inputAdapter->getDomain());
            $useCase = $this->studlyNames($inputAdapter->getUseCase());

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

            $template = $this->getTemplate('output-adapter');

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
            return GenerateOutputAdapterOutputAdapter::ofError($e->getMessage());
        }

        return GenerateOutputAdapterOutputAdapter::ofSuccess();
    }
}
