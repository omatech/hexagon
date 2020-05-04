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
        $domain = $this->studlyNames($inputAdapter->getDomain());
        $useCase = $this->studlyNames($inputAdapter->getUseCase());

        $pathPrefix = config('hexagon.directories.application', 'app/Application/');
        $pathPrefix = rtrim($pathPrefix, '/') . '/';
        $pathPost = config('hexagon.directories.output-adapter', '');

        $path = base_path($pathPrefix) . $domain . '/' . $useCase . '/' . $pathPost;

        $file =  $useCase . 'OutputAdapter.php';

        if (file_exists($path . '/' . $file) && !$inputAdapter->isOverwrite()) {
            return GenerateOutputAdapterOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('output-adapter');

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('UseCase', $useCase, $template);
        $template = $this->replace('useCase', lcfirst($useCase), $template);
        $template = $this->replace('Post', $pathPost, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateOutputAdapterOutputAdapter::ofError($e->getMessage());
        }

        return GenerateOutputAdapterOutputAdapter::ofSuccess();
    }
}
