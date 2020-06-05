<?php

namespace Omatech\Hexagon\Infrastructure\Commands;

use Illuminate\Console\Command;
use Omatech\Hexagon\Infrastructure\Menu\ActionMenu;
use Omatech\Hexagon\Infrastructure\Menu\DomainObjectMenu;
use Omatech\Hexagon\Infrastructure\Menu\UseCaseMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\GoBackAction;
use Illuminate\Support\Str;

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
    /** @var array */
    private $keys;

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
        $title = config('hexagon.menu.main.title', 'Welcome to Hexagonal For Laravel, ');

        $boundaries = config('hexagon.boundaries');

        $menu = (new CliMenuBuilder)->enableAutoShortcuts();

        if (!empty($boundaries)) {
            $title .= 'Please select a Boundary Context';

            $this->keys = ['N'];
            foreach ($boundaries as $boundary) {
                $title = Str::studly($boundary);


                $title = $this->getShortcutKey($title) ?? $title;

                $menu->addSubMenu($title, function (CliMenuBuilder $b) use ($boundary) {
                    $b = $this->addActionItems($b, $boundary);
                    $b->disableDefaultItems()
                        ->setTitle('Choose an Action')
                        ->addItem('Back To Main Menu', new GoBackAction);
                });
            }

            $menu->addSubMenu('[N]o Context', function (CliMenuBuilder $b) {
                $b = $this->addActionItems($b, null);
                $b->disableDefaultItems()
                    ->setTitle('Choose an Action')
                    ->addItem('Back To Main Menu', new GoBackAction);
            });


        } else {
            $title .= 'Please select an Action';
            $menu = $this->addActionItems($menu);
        }

        $menu->setBorder(1, 2, 'yellow')->setPadding(2, 4)->setMarginAuto();

        $menu->setTitle($title)->build()->open();

        $this->info('See you next time!');
    }

    private function addActionItems(CliMenuBuilder $menu, string $boundary = null): CliMenuBuilder
    {
        $menu->addItem('Generate [U]se Case', function (CliMenu $parentMenu) use ($boundary) {
            $this->useCaseMenu->show($parentMenu, $boundary);
        });

        if (config('hexagon.depth', 1) > 1) {
            $menu->addItem('Generate Domain [O]bject', function (CliMenu $parentMenu) use ($boundary) {
                $this->domainObjectMenu->show($parentMenu, $boundary);
            });
        }

        if (config('hexagon.depth', 1) > 2) {
            $menu->addItem('Generate [A]ction', function (CliMenu $parentMenu) use ($boundary) {
                $this->actionMenu->show($parentMenu, $boundary);
            })->addLineBreak('-');
        }

        return $menu;
    }

    private function getShortcutKey(string $text): ?string
    {
        for($i = 0; $i < strlen($text); $i++) {
            if (!in_array($text[$i], $this->keys)) {
                $this->keys[] = $text[$i];
                return substr_replace($text, '[' . ucfirst($text[$i]) . ']', $i, 1);
            }
        }

        return null;
    }
}
