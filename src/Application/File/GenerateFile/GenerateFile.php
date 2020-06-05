<?php

namespace Omatech\Hexagon\Application\File\GenerateFile;

use Omatech\Hexagon\Domain\File\File;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;
use Omatech\Hexagon\Domain\Template\Template;

final class GenerateFile
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var Template */
    private $template;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        GetRepository $getRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
        $this->getRepository = $getRepository;
    }

    public function execute(GenerateFileInputAdapter $request): GenerateFileOutputAdapter
    {
        $file = new File($request->getName(), $request->getType(), $request->getLayer(), $request->getDomain(), $request->getBoundary());

        $path = $file->getPath($file->pathHasDomain());

        if ($file->exists($path) && !$request->isOverwrite()) {
            return GenerateFileOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $this->template = $this->getRepository->execute($request->getType());

        $this->template->replace('Domain', $file->getDomain());
        $this->template->replace('Name', $file->getName());
        $this->template->replace('Namespace', $file->getNamespace($path));
        $this->template->replace('DomainNamespace', $file->getLayerNamespace('domain'));

        foreach ($file->getDependencies() as $dependency) {
            try {
                $this->replaceDependency($dependency, $request, $path);
            } catch (\Exception $e) {
                continue;
            }
        }
        $this->template->clean();

        try {
            $this->instantiateRepository->execute($this->template->getContent(), base_path($path), $file->getName('.php'));
        } catch (\Exception $e) {
            return GenerateFileOutputAdapter::ofError($e->getMessage());
        }

        return GenerateFileOutputAdapter::ofSuccess($request->getName());
    }

    private function replaceDependency(array $dependency, GenerateFileInputAdapter $request, $originalPath = ''): void
    {
        $dependencyFile = new File(
            $dependency['name'],
            $dependency['type'],
            $dependency['layer'],
            $dependency['domain'],
            $dependency['boundary']);

        $path = $dependencyFile->getPath();

        $dependencyUse = $dependencyFile->getType() . 'Use';
        $dependencyName = $dependencyFile->getType() . 'Name';

        if ($path !== $originalPath) {
            $this->template->replace($dependencyUse, $dependencyFile->getUse($path), false);
        }

        $this->template->replace($dependencyName, $dependencyFile->getName());
    }
}
