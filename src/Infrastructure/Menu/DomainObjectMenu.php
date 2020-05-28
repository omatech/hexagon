<?php

namespace Omatech\Hexagon\Infrastructure\Menu;

use Omatech\Hexagon\Application\File\GenerateFile\GenerateFile;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileInputAdapter;
use Omatech\Hexagon\Application\File\GenerateFile\GenerateFileOutputAdapter;
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
    /** @var GenerateFile */
    private $generateFile;
    /** @var string */
    private $domain;

    public function __construct(GenerateFile $generateFile)
    {
        $this->generateFile = $generateFile;
    }

    public function show(CliMenu $parentMenu, string $boundary = null)
    {
        $parentMenu->closeThis();

        $subMenu = new CliMenu('Choose a Domain', [], $parentMenu->getTerminal(), $parentMenu->getStyle());

        $subMenu->setParent($parentMenu);

        $domainList = $this->getDomainList($boundary);

        foreach ($domainList as $domainItem) {
            $subMenu->addItem(new SelectableItem($domainItem, function(CliMenu $menu) use (&$message, $boundary)
            {
                $this->domain = $menu->getSelectedItem()->getText();

                $message = $this->generateDomainObject($menu, $boundary);

                $style = (new MenuStyle($menu->getTerminal()))
                    ->setBg('blue')
                    ->setFg('white');

                $flash = new Flash($menu, $style, $menu->getTerminal(), $message);
                $flash->display();

                $menu->closeThis();
            }));
        }

        $subMenu->addItem(new SelectableItem('New Domain', function(CliMenu $menu) use (&$message, $boundary)
        {
            $this->domain = $this->prompt('domain', $menu);

            $message = $this->generateDomainObject($menu, $boundary);

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

    private function generateDomainObject(CliMenu $menu, string $boundary = null): string
    {
        // Generate Domain Object

        /** @var  GenerateFileOutputAdapter $generateFileOutputAdapter */
        $generateFileOutputAdapter = $this->generateFile->execute(
            new GenerateFileInputAdapter(
                $this->domain,
                'domain-object',
                'domain',
                $this->domain,
                true,
                $boundary ?? ''
            )
        );

        return $generateFileOutputAdapter->getOriginalContent()['message'];
    }
}
