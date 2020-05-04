<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\DomainObject\GenerateDomainObject;
use Omatech\Hexagon\Application\DomainObject\GenerateDomainObjectInputAdapter;
use Omatech\Hexagon\Application\DomainObject\GenerateDomainObjectOutputAdapter;
use PhpSchool\CliMenu\CliMenu;
//use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Dialogue\Flash;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\MenuItem\LineBreakItem;
use PhpSchool\CliMenu\MenuStyle;

class DomainObjectMenu extends Menu
{
    /** @var GenerateDomainObject */
    private $generateDomainObject;

    public function __construct(GenerateDomainObject $generateDomainObject)
    {
        $this->generateDomainObject = $generateDomainObject;
    }

    public function show(CliMenu $parentMenu)
    {
        $parentMenu->closeThis();

        $subMenu = new CliMenu('Choose a Domain', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->setParent($parentMenu);

        $domainList = $this->getDomainList();

        foreach ($domainList as $domainItem) {
            $subMenu->addItem(new SelectableItem($domainItem, function(CliMenu $menu) use (&$message)
            {
                $this->domain = $menu->getSelectedItem()->getText();

                $message = $this->generateDomainObject($menu);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }, false, true));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) use (&$message)
        {
            $this->domain = $this->prompt('domain', $menu);

            $message = $this->generateDomainObject($menu);

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

        $this->domain = null;
    }

    private function generateDomainObject(CliMenu $menu): string
    {
        // Generate Domain Object

        /** @var  GenerateDomainObjectOutputAdapter $generateDomainObjectOutputAdapter */
        $generateDomainObjectOutputAdapter = $this->generateDomainObject->execute(new GenerateDomainObjectInputAdapter($this->domain));

        return $generateDomainObjectOutputAdapter->getOriginalContent()['message'];
    }
}
