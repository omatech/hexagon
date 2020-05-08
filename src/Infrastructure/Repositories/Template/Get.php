<?php

namespace Omatech\Hexagon\Infrastructure\Repositories\Template;

use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\Template;

class Get implements GetRepository
{
    public function execute(string $template): Template
    {
        $path = $this->getTemplatePath($template);

        return new Template(file_get_contents($path));
    }

    protected function getTemplatePath($template): string
    {
        $template = preg_replace(["/[^a-zA-Z\d]/", '/__+/'], '', $template);

        $templatesDirectory = resource_path(config('hexagon.directories.templates'));
        $templatesDefaultDirectory = base_path(config('hexagon.directories.templates_default'));

        $path = $templatesDirectory . $template . '.stub';

        if (file_exists($path)) {
            return $path;
        }

        $path = $templatesDefaultDirectory . $template . '.stub';

        if (file_exists($path)) {
            return $path;
        }

        return base_path('vendor/omatech/hexagon/resources/templates/' . $template . '.stub');
    }
}