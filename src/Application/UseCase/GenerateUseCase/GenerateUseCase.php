<?php

namespace Omatech\Hexagon\Application\UseCase\GenerateUseCase;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateUseCase
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

        $template->replace('Domain', $domain);
        $template->replace('UseCase', $useCase);

        $inputUse = null;
        $outputUse = null;

        if (!empty($inputAdapterFolder)) {
            $inputAdapterFolder =  trim($inputAdapterFolder, '/') . '\\';
            $inputUse = PHP_EOL . 'use App\Application\\' . $domain . '\\' . $useCase . '\\' . $inputAdapterFolder .
                $useCase . $inputAdapterName . ';' . PHP_EOL;
            $template->replace('InputUse', $inputUse);
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

        $template->replace('InputName', $inputAdapterName ?? 'InputAdapter');
        $template->replace('OutputName', $outputAdapterName ?? 'OutputAdapter');
        $template->replace('InputUse', $inputUse ?? '', false);
        $template->replace('OutputUse', $outputUse ?? '', false);

//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateUseCaseOutputAdapter::ofError($e->getMessage());
        }

        return GenerateUseCaseOutputAdapter::ofSuccess();
    }
}
