<?php

namespace Omatech\Hexagon\Infrastructure\Commands;

use Illuminate\Console\Command;
use Omatech\Hexagon\Infrastructure\Menu\ActionMenu;
use Omatech\Hexagon\Infrastructure\Menu\DomainObjectMenu;
use Omatech\Hexagon\Infrastructure\Menu\UseCaseMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;

final class HexagonalCLI extends Command
{
    protected $signature = 'hexagon';

    protected $description = 'Generate Project Scaffold CLI';

    /** @var UseCaseMenu */
    private $useCaseMenu;
    /** @var DomainObjectMenu */
    private $domainObjectMenu;
    /** @var ActionMenu */
    private $actionMenu;

    public function __construct(
        UseCaseMenu $useCaseMenu,
        DomainObjectMenu $domainObjectMenu,
        ActionMenu $actionMenu
    )
    {
        parent::__construct();
        $this->useCaseMenu = $useCaseMenu;
        $this->domainObjectMenu = $domainObjectMenu;
        $this->actionMenu = $actionMenu;
    }

    public function handle()
    {
        $this->showMainMenu();
    }

    private function showMainMenu()
    {
            $title = config('hexagon.menu.main.title', 'Welcome to Hexagonal For Laravel, Please select an option');

            $menu = (new CliMenuBuilder)
                ->enableAutoShortcuts()
                ->setTitle($title)
                ->addItem('Generate [U]se Case', function (CliMenu $parentMenu) {
                    $this->useCaseMenu->show($parentMenu);
                });

            if (config('hexagon.depth', 1) > 1) {
                $menu->addItem('Generate Domain [O]bject', function (CliMenu $parentMenu) {
                    $this->domainObjectMenu->show($parentMenu);
                });
            }

            if (config('hexagon.depth', 1) > 2) {
                $menu->addItem('Generate [A]ction', function (CliMenu $parentMenu) {
                    $this->actionMenu->show($parentMenu);
                })->addLineBreak('-');
            }

            $menu->setBorder(1, 2, 'yellow')
                ->setPadding(2, 4)
                ->setMarginAuto();

        $menu->build()->open();

        $this->info('See you next time!');
    }
}
