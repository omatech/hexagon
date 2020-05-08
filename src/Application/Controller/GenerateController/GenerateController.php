<?php

namespace Omatech\Hexagon\Application\Controller\GenerateController;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateController
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

        $template->replace('Domain', $domain);
        $template->replace('UseCase', $useCase);
        $template->replace('useCase', lcfirst($useCase));
        $template->replace('Namespace', $namespace);
        $template->replace('InputName', $inputName);
        $template->replace('OutputName', $outputName);
        $template->replace('InputFolder', $inputFolder ?? '');
        $template->replace('OutputFolder', $outputFolder ?? '');
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateControllerOutputadapter::ofError($e->getMessage());
        }

        return GenerateControllerOutputAdapter::ofSuccess();
    }
}
