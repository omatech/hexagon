<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\UseCase\Exception\UseCaseAlreadyExistsException;
use Omatech\Hexagon\Application\Action\Exception\ActionAlreadyExistsException;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\MenuStyle;

class Menu
{
    const CONTROLLER_TYPES = [
        'Api' => 'Api',
        'Http' => 'Http',
        'ApiHttp' => 'Create both, Api and Http',
        'None' => 'Do not create a controller'];

    protected function confirmOverwrite(string $fileName, CliMenu $parentMenu, string $prompt = ''): bool
    {
        $overwrite = null;

        $prompt = (empty($prompt))
            ? $fileName.' already exists. Do you want to overwrite it?'
            : $prompt;

        $subMenu = new CliMenu($prompt, [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->addItem(new SelectableItem('Yes', function(CliMenu $menu) use (&$overwrite) {
            $overwrite = $menu->getSelectedItem()->getText();
            $menu->closeThis();
        }));

        $subMenu->addItem(new SelectableItem('No', function(CliMenu $menu) use (&$overwrite) {
            $overwrite = $menu->getSelectedItem()->getText();
            $menu->closeThis();
        }));

        $subMenu->open();

        return $overwrite === 'Yes';
    }

    protected function prompt(string $text, CliMenu $menu): string
    {
        $text = ucwords($text);

        $popupStyle = (new MenuStyle)
            ->setBg('blue')
            ->setFg('white')
            ->setBorder(10, 'white');


        do {
            $input = $menu->askText($popupStyle)
                ->setPromptText("Enter $text name")
                ->setPlaceholderText($text)
                ->setValidationFailedText("Please enter a $text name")
                ->ask()
                ->fetch();
        } while ($input && $input == $text);

        return $input;
    }

    /** @throws UseCaseAlreadyExistsException */
    protected function checkUseCase(string $domain, string $useCase, CliMenu $menu): string
    {
        $useCaseDirectory = config('hexagonal.directories.application', 'app/Application');
        $useCaseDirectory = rtrim(base_path($useCaseDirectory),'/');
        $useCaseDirectory = $useCaseDirectory . '/' . $domain;

        $useCaseList = $this->getDirectoryContent($useCaseDirectory);

        if (in_array($useCase, $useCaseList)) {
            $prompt = 'The use Case ' . $useCase. ' in domain ' . $domain . ' already exists, overwrite?';
            $overwrite = $this->confirmOverwrite($useCase, $menu, $prompt);
            $name = null;

            if (!$overwrite) throw UseCaseAlreadyExistsException::fromException(new \Exception());

            return true;
        }

        return false;
    }

    /** @throws ActionAlreadyExistsException */
    protected function checkAction(string $domain, string $action, CliMenu $menu): string
    {
        $actionDirectory = config('hexagonal.directories.infrastructure', 'app/Infrastructure');
        $actionDirectory = rtrim($actionDirectory,'/');
        $actionDirectory = $actionDirectory . '/' . $domain;

        $actionList = $this->getDirectoryContent($actionDirectory);

        if (in_array($action, $actionList)) {
            $prompt = 'The action ' . $action. ' in domain ' . $domain . ' already exists, overwrite?';
            $overwrite = $this->confirmOverwrite($action, $menu, $prompt);
            $name = null;

            if (!$overwrite) throw ActionAlreadyExistsException::fromException(new \Exception());

            return true;
        }

        return false;
    }

    protected function getDirectoryContent(string $directory): array
    {
        try {
            $directoryList = scandir($directory);
        } catch (\Exception $e) {
            mkdir($directory);
            $directoryList = scandir($directory);
        }

        $directoryList = array_diff($directoryList, array('..', '.', 'Base'));
        $directoryList = array_values($directoryList);

        return array_filter($directoryList, function ($item)
        {
            return !strpos($item, '.');
        });
    }

    protected function requireControllerType(CliMenu $parentMenu): ?string
    {
        $subMenu = new CliMenu('Choose a Controller Type', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $controllerTypes = self::CONTROLLER_TYPES;

        $controllerType = null;

        foreach ($controllerTypes as $type => $text) {
            $subMenu->addItem(new SelectableItem($text, function(CliMenu $menu) use (&$controllerType, $type) {
                $controllerType = $type;
                $menu->closeThis();
            }));
        }

        $subMenu->open();

        return $controllerType;
    }

    protected function getDomainList($layer = 'Domain'): array
    {
        $domainDirectory = config('hexagonal.directories.domain', base_path('app/'));
        $domainDirectory .= $layer;

        $domainList = $this->getDirectoryContent($domainDirectory);

        return array_filter($domainList, function ($item)
        {
            return !strpos($item, '.');
        });
    }
}
