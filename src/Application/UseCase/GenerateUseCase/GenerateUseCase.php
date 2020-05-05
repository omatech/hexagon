<?php

namespace Omatech\Hexagon\Application\UseCase\GenerateUseCase;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\GetRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\ReplaceRepository;

final class GenerateUseCase
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

    public function execute(GenerateUseCaseInputAdapter $request): GenerateUseCaseOutputAdapter
    {
        $domain = $this->stringToStudlyCaseRepository->execute($request->getDomain());
        $useCase = $this->stringToStudlyCaseRepository->execute($request->getUseCase());

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

        $template = $this->getRepository->execute('use-case');

        $template = $this->replaceRepository->execute('Domain', $domain, $template);
        $template = $this->replaceRepository->execute('UseCase', $useCase, $template);

        $inputUse = null;
        $outputUse = null;

        if (!empty($inputAdapterFolder)) {
            $inputAdapterFolder =  trim($inputAdapterFolder, '/') . '\\';
            $inputUse = PHP_EOL . 'use App\Application\\' . $domain . '\\' . $useCase . '\\' . $inputAdapterFolder .
                $useCase . $inputAdapterName . ';' . PHP_EOL;
            $template = $this->replaceRepository->execute('InputUse', $inputUse, $template);
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

        $template = $this->replaceRepository->execute('InputName', $inputAdapterName ?? 'InputAdapter', $template);
        $template = $this->replaceRepository->execute('OutputName', $outputAdapterName ?? 'OutputAdapter', $template);
        $template = $this->replaceRepository->execute('InputUse', $inputUse ?? '', $template, false);
        $template = $this->replaceRepository->execute('OutputUse', $outputUse ?? '', $template, false);

//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateUseCaseOutputAdapter::ofError($e->getMessage());
        }

        return GenerateUseCaseOutputAdapter::ofSuccess();
    }
}
