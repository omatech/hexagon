<?php

namespace Omatech\Hexagon\Application\Controller\GenerateController;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\GetRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\ReplaceRepository;

final class GenerateController
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

    public function execute(GenerateControllerInputAdapter $request): GenerateControllerOutputAdapter
    {
        $domain = $this->stringToStudlyCaseRepository->execute($request->getDomain());
        $useCase = $this->stringToStudlyCaseRepository->execute($request->getUseCase());
        $type = ucfirst($request->getType());

        $infrastructurePath = config('hexagon.directories.infrastructure', 'app/Infrastructure/');
        $path = base_path($infrastructurePath) . $type . '/Controllers';

        $file = $useCase . 'Controller.php';

        $namespaceRoute = str_replace('/', '\\', ucfirst($infrastructurePath));

        $namespace = 'namespace ' . $namespaceRoute . $type . '\Controllers;';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateControllerOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getRepository->execute(strtolower($type).'-controller');

        $inputName = config('hexagon.directories.input-adapter.name', 'InputAdapter') ?? 'InputAdapter';
        $outputName = config('hexagon.directories.output-adapter.name', 'OutputAdapter') ?? 'OutputAdapter';
        $inputFolder = config('hexagon.directories.input-adapter.folder', '');
        $outputFolder = config('hexagon.directories.output-adapter.folder', '');

        if (!empty($inputFolder)) {
            $inputFolder =  trim($inputFolder, '/\\') . '\\';
        }

        if (!empty($outputFolder)) {
            $outputFolder =  trim($outputFolder, '/\\') . '\\';
        }

        $template = $this->replaceRepository->execute('Domain', $domain, $template);
        $template = $this->replaceRepository->execute('UseCase', $useCase, $template);
        $template = $this->replaceRepository->execute('useCase', lcfirst($useCase), $template);
        $template = $this->replaceRepository->execute('Namespace', $namespace, $template);
        $template = $this->replaceRepository->execute('InputName', $inputName, $template);
        $template = $this->replaceRepository->execute('OutputName', $outputName, $template);
        $template = $this->replaceRepository->execute('InputFolder', $inputFolder ?? '', $template);
        $template = $this->replaceRepository->execute('OutputFolder', $outputFolder ?? '', $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateControllerOutputadapter::ofError($e->getMessage());
        }

        return GenerateControllerOutputAdapter::ofSuccess();
    }
}
