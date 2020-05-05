<?php

namespace Omatech\Hexagon\Application\Base;

use Illuminate\Support\Str;

trait Instantiatable
{
    protected function getTemplate($template)
    {
        $path = $this->getTemplatePath($template);

        return file_get_contents($path);
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

    protected function replace(string $variable, string $value, string $template, $caps = true): string
    {
        if ($caps) {
            $value = ucfirst($value);
        }

        return str_replace('${' . $variable . '}', $value, $template);
    }

//    protected function clearTemplate($template)
//    {
//        return preg_replace(["/\${(.*)}/"], '', $template);
//    }

    private function studlyNames(string $name): string
    {
        $name = preg_replace(["/[^a-zA-Z\d]/", '/__+/'], ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return Str::studly($name);
    }
}
