<?php

namespace Omatech\Hexagon\Application\Action\GenerateAction;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;

final class GenerateAction
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateActionInputAdapter $request): GenerateActionOutputAdapter
    {
        $domain = $this->studlyNames($request->getDomain());
        $action = $this->studlyNames($request->getAction());

        $path = config('hexagon.directories.action', 'app/Infrastructure/');
        $path = base_path($path) . $domain;

        $file = $action . '.php';

        if (file_exists($path . '/' . $file) && !$request->isOverwrite()) {
            return GenerateActionOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('action');

        $template = $this->replace('Domain', $domain, $template);
        $template = $this->replace('Action', $action, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateActionOutputAdapter::ofError($e->getMessage());
        }

        return GenerateActionOutputAdapter::ofSuccess();
    }
}
