<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\Action\GenerateAction;
use Omatech\Hexagon\Application\Action\GenerateActionInputAdapter;
use Omatech\Hexagon\Application\Action\GenerateActionOutputAdapter;
use Omatech\Hexagon\Application\ActionRepository\GenerateActionRepository;
use Omatech\Hexagon\Application\ActionRepository\GenerateActionRepositoryInputAdapter;
use Omatech\Hexagon\Application\ActionRepository\GenerateActionRepositoryOutputAdapter;
use Omatech\Hexagon\Domain\Action\Exception\ActionAlreadyExistsException;
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

    /** @var GenerateAction */
    private $generateAction;
    /** @var GenerateActionRepository */
    private $generateActionRepository;
    /** @var string */
    private $domain;
    /** @var string */
    private $action;

    public function __construct(
        GenerateAction $generateAction,
        GenerateActionRepository $generateActionRepository
    )
    {
        $this->generateAction = $generateAction;
        $this->generateActionRepository = $generateActionRepository;
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

                $message = $this->generateAction($menu);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) {

            $message = $this->generateAction($menu);

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

    private function generateAction(CliMenu $menu): string
    {
        while (!$this->domain){
            $this->domain = $this->prompt('domain', $menu);
        }

        do {
            $this->action = $this->prompt('action', $menu);
            $overwrite = false;
            try {
                $overwrite = $this->checkAction($this->domain, $this->action, $menu);
            } catch (ActionAlreadyExistsException | DirectoryDoesNotExistException $e) {
                $this->action = null;
            }

        } while(!$this->action && !$overwrite);


        // Generate Action
        /** @var GenerateActionOutputAdapter $actionOutputAdapter */
        $actionOutputAdapter = $this->generateAction->execute(
            new GenerateActionInputAdapter($this->domain, $this->action, $overwrite)
        );

        if ($actionOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($actionOutputAdapter->getOriginalContent()['message']);
        }

        // Generate Action Repository
        /** @var GenerateActionRepositoryOutputAdapter $actionOutputAdapter */
        $actionOutputAdapter = $this->generateActionRepository->execute(
            new GenerateActionRepositoryInputAdapter($this->domain, $this->action, $overwrite)
        );

        // TODO: BindRepository

        if ($actionOutputAdapter->getStatusCode() != 200) {
            $menu->confirm($actionOutputAdapter->getOriginalContent()['message']);
        }

        $this->domain = null;

        return 'Action created Successfully!';
    }
}
