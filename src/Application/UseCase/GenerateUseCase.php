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

        $file = $useCase . '.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateUseCaseOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('use-case');

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('UseCase', $useCase, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateUseCaseOutputAdapter::ofError($e->getMessage());
        }

        return GenerateUseCaseOutputAdapter::ofSuccess();
    }
}
