<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\File\GenerateFile\GenerateFile;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileInputAdapter;
use Omatech\Hexagon\Application\ActionRepository\BindRepository\BindRepository;
use Omatech\Hexagon\Application\ActionRepository\BindRepository\BindRepositoryInputAdapter;
use Omatech\Hexagon\Application\ActionRepository\BindRepository\BindRepositoryOutputAdapter;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileOutputAdapter;
use Omatech\Hexagon\Domain\Base\Exceptions\DirectoryDoesNotExistException;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Dialogue\Flash;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\MenuItem\LineBreakItem;
use PhpSchool\CliMenu\MenuStyle;

class ActionMenu extends Menu
{

    /** @var GenerateFile */
    private $generateFile;
    /** @var string */
    private $domain;
    /** @var string */
    private $action;
    /** @var BindRepository */
    private $bindRepository;

    public function __construct(
        GenerateFile $generateFile,
        BindRepository $bindRepository
    )
    {
        $this->generateFile = $generateFile;
        $this->bindRepository = $bindRepository;
    }

    public function show(CliMenu $parentMenu, string $boundary = null)
    {
        $parentMenu->closeThis();

        $subMenu = new CliMenu('Choose a Domain', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->setParent($parentMenu);

        $actionFolder = rtrim(config('hexagonal.directories.infrastructure', 'Infrastructure'), '/') . '/';
        $actionFolder .= rtrim(config('hexagonal.directories.action', 'Repositories'), '/') . '/';

        $domainList = $this->getDomainList($boundary);
//        try {
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//        }

        foreach ($domainList as $domainItem) {
            $subMenu->addItem(new SelectableItem($domainItem, function(CliMenu $menu) use ($boundary){
                $this->domain = $menu->getSelectedItem()->getText();

                $message = $this->generateAction($menu, $boundary);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) use ($boundary) {

            $message = $this->generateAction($menu, $boundary);

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

    private function generateAction(CliMenu $menu, string $boundary = null): string
    {
        while (!$this->domain){
            $this->domain = $this->prompt('domain', $menu);
        }

        do {
            $this->action = $this->prompt('action', $menu);
            $overwrite = false;
            try {
                $overwrite = $this->checkAction($this->domain, $this->action, $menu);
            } catch (DirectoryDoesNotExistException $e) {
                $this->action = null;
            }

        } while(!$this->action && !$overwrite);

        // Generate Action
        /** @var GenerateFileOutputAdapter $actionOutputAdapter */
        $actionOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter($this->action, 'action', 'infrastructure', $this->domain, $overwrite, $boundary)
        );

        if ($actionOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($actionOutputAdapter->getOriginalContent()['message']);
        }

        // Generate Action Repository
        /** @var GenerateFileOutputAdapter $actionOutputAdapter */
        $actionRepositoryOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter($this->action, 'action-repository', 'domain' ,$this->domain, $overwrite, $boundary)
        );

        if ($actionRepositoryOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($actionRepositoryOutputAdapter->getOriginalContent()['message']);
        }

        $interface = $actionRepositoryOutputAdapter->getOriginalContent()['class'] ?? null;
        $instance = $actionOutputAdapter->getOriginalContent()['class'] ?? null;

        if (!empty($interface) && !empty($instance)) {
            // TODO: BindRepository
            /** @var BindRepositoryOutputAdapter */
            $bindRepository = $this->bindRepository->execute(
                new BindRepositoryInputAdapter(
                    $this->domain,
                    $instance,
                    $interface,
                    $boundary
                )
            );

            if ($bindRepository->getStatusCode() != 200) {
                $menu->confirm($bindRepository->getOriginalContent()['message']);
            }
        }

        $this->domain = null;

        return 'Action created Successfully!';
    }
}
