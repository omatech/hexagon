<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\Controller\GenerateController\GenerateController;
use Omatech\Hexagon\Application\Controller\GenerateController\GenerateControllerInputAdapter;
use Omatech\Hexagon\Application\Controller\GenerateController\GenerateControllerOutputAdapter;
use Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter\GenerateInputAdapter;
use Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter\GenerateInputAdapterInputAdapter;
use Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter\GenerateInputAdapterOutputAdapter;
use Omatech\Hexagon\Application\OutputAdapter\GenerateOutputAdapter\GenerateOutputAdapter;
use Omatech\Hexagon\Application\OutputAdapter\GenerateOutputAdapter\GenerateOutputAdapterInputAdapter;
use Omatech\Hexagon\Application\OutputAdapter\GenerateOutputAdapter\GenerateOutputAdapterOutputAdapter;
use Omatech\Hexagon\Application\UseCase\GenerateUseCase\GenerateUseCase;
use Omatech\Hexagon\Application\UseCase\GenerateUseCase\GenerateUseCaseInputAdapter;
use Omatech\Hexagon\Application\UseCase\GenerateUseCase\GenerateUseCaseOutputAdapter;
use Omatech\Hexagon\Domain\Base\Exceptions\DirectoryDoesNotExistException;
use Omatech\Hexagon\Domain\UseCase\Exception\UseCaseAlreadyExistsException;
use PhpSchool\CliMenu\CliMenu;
//use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Dialogue\Flash;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\MenuItem\LineBreakItem;
use PhpSchool\CliMenu\MenuStyle;

class UseCaseMenu extends Menu
{
    /** @var GenerateController */
    private $generateController;
    /** @var GenerateInputAdapter */
    private $generateInputAdapter;
    /** @var GenerateOutputAdapter */
    private $generateOutputAdapter;
    /** @var GenerateUseCase */
    private $generateUseCase;
    /** @var string */
    private $domain;
    /** @var string */
    private $useCase;


    public function __construct(
        GenerateController $generateController,
        GenerateInputAdapter $generateInputAdapter,
        GenerateOutputAdapter $generateOutputAdapter,
        GenerateUseCase $generateUseCase
    )
    {
        $this->generateController = $generateController;
        $this->generateInputAdapter = $generateInputAdapter;
        $this->generateOutputAdapter = $generateOutputAdapter;
        $this->generateUseCase = $generateUseCase;
    }

    public function show(CliMenu $parentMenu)
    {
        $parentMenu->closeThis();

        $subMenu = new CliMenu('Choose a Domain', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->setParent($parentMenu);

        try {
            $domainList = $this->getDomainList('Application');
        } catch (\Exception $e) {

        }

        foreach ($domainList as $domainItem) {
            $subMenu->addItem(new SelectableItem($domainItem, function(CliMenu $menu) {
                $this->domain = $menu->getSelectedItem()->getText();

                $message = $this->generateUseCase($menu);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) {

            $message = $this->generateUseCase($menu);

            $style = (new MenuStyle($menu->getTerminal()))
                ->setBg('black')
                ->setFg('white');

            $flash = new Flash($menu, $style, $menu->getTerminal(), $message);

            $flash->display();

            $menu->closeThis();
        }));

        $subMenu->addItem(new LineBreakItem('-'));
        $subMenu->addItem(new SelectableItem('Go Back', new GoBackAction));
//        $subMenu->addItem(new SelectableItem('Exit', new ExitAction));

        $subMenu->open();

        $parentMenu->open();
    }

    private function generateUseCase(CliMenu $menu): string
    {
        while (!$this->domain){
            $this->domain = $this->prompt('domain', $menu);
        }

        do {
            $this->useCase = $this->prompt('use case', $menu);
            $overwrite = false;
            try {
                $overwrite = $this->checkUseCase($this->domain, $this->useCase, $menu);
            } catch (UseCaseAlreadyExistsException | DirectoryDoesNotExistException $e) {
                $this->useCase = null;
            }

        } while(!$this->useCase && !$overwrite);

        // Generate Controller
        $type = $this->requireControllerType($menu);

        if (\Str::contains($type,  'Api')) {
            /** @var  GenerateControllerOutputAdapter $apiControllerOutputAdapter */
            $apiControllerOutputAdapter = $this->generateController->execute(
                new GenerateControllerInputAdapter($this->domain, $this->useCase, $overwrite, 'Api')
            );

            if ($apiControllerOutputAdapter->getStatusCode() != 200) {
                $menu->confirm($apiControllerOutputAdapter->getOriginalContent()['message']);
            }
        }

        if (\Str::contains($type,  'Http')) {
            /** @var  GenerateControllerOutputadapter $httpControllerOutputAdapter */
            $httpControllerOutputAdapter = $this->generateController->execute(
                new GenerateControllerInputAdapter($this->domain, $this->useCase, $overwrite, 'Http')
            );

            if ($httpControllerOutputAdapter->getStatusCode() != 200) {
                $menu->confirm($httpControllerOutputAdapter->getOriginalContent()['message']);
            }
        }

        // Generate InputAdapter
        /** @var GenerateInputAdapterOutputAdapter $inputAdapterOutputAdapter */
        $inputAdapterOutputAdapter = $this->generateInputAdapter->execute(
            new GenerateInputAdapterInputAdapter($this->domain, $this->useCase, $overwrite)
        );

        if ($inputAdapterOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($inputAdapterOutputAdapter->getOriginalContent()['message']);
        }

        // Generate OutputAdapter
        /** @var GenerateOutputAdapterOutputAdapter $outputAdapterOutputAdapter */
        $outputAdapterOutputAdapter = $this->generateOutputAdapter->execute(
            new GenerateOutputAdapterInputAdapter($this->domain, $this->useCase, $overwrite)
        );

        if ($outputAdapterOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($outputAdapterOutputAdapter->getOriginalContent()['message']);
        }

        // Generate Use Case
        /** @var GenerateUseCaseOutputAdapter $useCaseOutputAdapter */
        $useCaseOutputAdapter = $this->generateUseCase->execute(
            new GenerateUseCaseInputAdapter($this->domain, $this->useCase, $overwrite)
        );

        if ($useCaseOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($useCaseOutputAdapter->getOriginalContent()['message']);
        }

        $this->domain = null;

        return 'Use Case created Successfully!';
    }
}
