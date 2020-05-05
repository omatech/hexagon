<?php

namespace Omatech\Hexagon\Application\Controller;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;

final class GenerateController
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateControllerInputAdapter $request): GenerateControllerOutputAdapter
    {
        $domain = $this->studlyNames($request->getDomain());
        $useCase = $this->studlyNames($request->getUseCase());
        $type = ucfirst($request->getType());

        $infrastructurePath = config('hexagon.directories.infrastructure', 'app/Infrastructure/');
        $path = base_path($infrastructurePath) . $type . '/Controllers';

        $file = $useCase . 'Controller.php';

        $namespaceRoute = str_replace('/', '\\', ucfirst($infrastructurePath));

        $namespace = 'namespace ' . $namespaceRoute . $type . '\Controllers;';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateControllerOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate(strtolower($type).'-controller');

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

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('UseCase', $useCase, $template);
        $template = $this->replace('useCase', lcfirst($useCase), $template);
        $template = $this->replace('Namespace', $namespace, $template);
        $template = $this->replace('InputName', $inputName, $template);
        $template = $this->replace('OutputName', $outputName, $template);
        $template = $this->replace('InputFolder', $inputFolder ?? '', $template);
        $template = $this->replace('OutputFolder', $outputFolder ?? '', $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateControllerOutputadapter::ofError($e->getMessage());
        }

        return GenerateControllerOutputAdapter::ofSuccess();
    }
}
