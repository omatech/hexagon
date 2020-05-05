<?php

namespace Omatech\Hexagon\Application\UseCase;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\UseCase\Exception\UseCaseCouldNotBeInstantiated;

final class GenerateUseCase
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateUseCaseInputAdapter $request): GenerateUseCaseOutputAdapter
    {
        $domain = $this->studlyNames($request->getDomain());
        $useCase = $this->studlyNames($request->getUseCase());

        $path = config('hexagon.directories.application', 'app/Application/');
        $path = rtrim($path, '/') . '/';
        $path = base_path($path) . $domain . '/' . $useCase;
        $inputAdapterFolder =  config('hexagon.directories.input-adapter.folder', '');
        $outputAdapterFolder =  config('hexagon.directories.output-adapter.folder', '');
        $inputAdapterName = config('hexagon.directories.input-adapter.name', 'InputAdapter');
        $outputAdapterName = config('hexagon.directories.output-adapter.name', 'OutputAdapter');

        $file = $useCase . '.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateUseCaseOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('use-case');

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('UseCase', $useCase, $template);

        $inputUse = null;
        $outputUse = null;

        if (!empty($inputAdapterFolder)) {
            $inputAdapterFolder =  trim($inputAdapterFolder, '/') . '\\';
            $inputUse = PHP_EOL . 'use App\Application\\' . $domain . '\\' . $useCase . '\\' . $inputAdapterFolder .
                $useCase . $inputAdapterName . ';' . PHP_EOL;
            $template = $this->replace('InputUse', $inputUse, $template);
        } else {
            if (!empty($outputAdapterFolder)) {
                $inputUse = PHP_EOL;
            }
        }

        if (!empty($outputAdapterFolder)) {
            $outputAdapterFolder =  trim($outputAdapterFolder, '/') . '\\';
            $outputUse = 'use App\Application\\' . $domain . '\\' . $useCase . '\\' . $outputAdapterFolder .
                $useCase . $outputAdapterName . ';' . PHP_EOL;
        }

        $template = $this->replace('InputName', $inputAdapterName ?? 'InputAdapter', $template);
        $template = $this->replace('OutputName', $outputAdapterName ?? 'OutputAdapter', $template);
        $template = $this->replace('InputUse', $inputUse ?? '', $template, false);
        $template = $this->replace('OutputUse', $outputUse ?? '', $template, false);

//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateUseCaseOutputAdapter::ofError($e->getMessage());
        }

        return GenerateUseCaseOutputAdapter::ofSuccess();
    }
}
