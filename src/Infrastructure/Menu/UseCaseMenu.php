<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\Controller\GenerateController\GenerateController;
use Omatech\Hexagon\Application\Controller\GenerateController\GenerateControllerInputAdapter;
use Omatech\Hexagon\Application\Controller\GenerateController\GenerateControllerOutputAdapter;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFile;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileInputAdapter;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileOutputAdapter;
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
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Dialogue\Flash;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\MenuItem\LineBreakItem;
use PhpSchool\CliMenu\MenuStyle;
use Illuminate\Support\Str;

class UseCaseMenu extends Menu
{
    /** @var string */
    private $domain;
    /** @var string */
    private $useCase;
    /** @var GenerateFile */
    private $generateFile;


    public function __construct(GenerateFile $generateFile)
    {
        $this->generateFile = $generateFile;
    }

    public function show(CliMenu $parentMenu, string $boundary = null)
    {
        $parentMenu->closeThis();

        $subMenu = new CliMenu('Choose a Domain', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->setParent($parentMenu);

        try {
            $domainList = $this->getDomainList($boundary);
        } catch (\Exception $e) {

        }

        foreach ($domainList as $domainItem) {
            $subMenu->addItem(new SelectableItem($domainItem, function(CliMenu $menu) use ($boundary) {
                $this->domain = $menu->getSelectedItem()->getText();

                $message = $this->generateUseCase($menu, $boundary);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) use ($boundary){

            $message = $this->generateUseCase($menu, $boundary);

            $style = (new MenuStyle($menu->getTerminal()))
                ->setBg('black')
                ->setFg('white');

            $flash = new Flash($menu, $style, $menu->getTerminal(), $message);

            $flash->display();

            $menu->closeThis();
        }));

        $subMenu->addItem(new LineBreakItem('-'));
        $subMenu->addItem(new SelectableItem('Go Back', new GoBackAction));

        $subMenu->open();

        $parentMenu->open();
    }

    private function generateUseCase(CliMenu $menu, string $boundary = null): string
    {
        while (!$this->domain){
            $this->domain = $this->prompt('domain', $menu);
        }

        do {
            $this->useCase = $this->prompt('use case', $menu);
            $overwrite = false;
            try {
                $overwrite = $this->checkUseCase($this->domain, $this->useCase, $menu);
            } catch (DirectoryDoesNotExistException $e) {
                $this->useCase = null;
            }

        } while(!$this->useCase && !$overwrite);

        // Generate Controller
        $type = $this->requireControllerType($menu);

        if (Str::contains($type,  'Api')) {
            /** @var  GenerateFileOutputAdapter $apiControllerOutputAdapter */
            $apiControllerOutputAdapter = $this->generateFile->execute(
                new GenerateFileInputAdapter(
                    $this->useCase,
                    'api-controller',
                    'infrastructure',
                    $this->domain,
                    $overwrite,
                    $boundary
                )
            );

            if ($apiControllerOutputAdapter->getStatusCode() != 200) {
                $menu->confirm($apiControllerOutputAdapter->getOriginalContent()['message']);
            }
        }

        if (Str::contains($type,  'Http')) {
            /** @var  GenerateFileOutputAdapter $apiControllerOutputAdapter */
            $httpControllerOutputAdapter = $this->generateFile->execute(
                new GenerateFileInputAdapter(
                    $this->useCase,
                    'http-controller',
                    'infrastructure',
                    $this->domain,
                    $overwrite,
                    $boundary
                )
            );

            if ($httpControllerOutputAdapter->getStatusCode() != 200) {
                $menu->confirm($httpControllerOutputAdapter->getOriginalContent()['message']);
            }
        }

        // Generate InputAdapter
        $inputAdapterOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter(
                $this->useCase,
                'input-adapter',
                'application',
                $this->domain,
                $overwrite,
                $boundary)
        );

        if ($inputAdapterOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($inputAdapterOutputAdapter->getOriginalContent()['message']);
        }

        // Generate OutputAdapter
        $outputAdapterOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter(
                $this->useCase,
                'output-adapter',
                'application',
                $this->domain,
                $overwrite,
                $boundary)
        );

        if ($outputAdapterOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($outputAdapterOutputAdapter->getOriginalContent()['message']);
        }

        // Generate Use Case
        $useCaseOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter(
                $this->useCase,
                'use-case',
                'application',
                $this->domain,
                $overwrite,
                $boundary
            )
        );

        if ($useCaseOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($useCaseOutputAdapter->getOriginalContent()['message']);
        }

        $this->domain = null;

        return 'Use Case created Successfully!';
    }
}
