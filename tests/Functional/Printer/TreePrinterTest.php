<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\Functional\Printer;

use Medas\Console\Tree;
use Medas\ConsolePrinter\{ConsolePrinter, Trees\TreePrinter};
use Medas\ConsolePrinterTest\Functional\BaseTestClass;

class TreePrinterTest extends BaseTestClass
{
    public function testBasicTree(): void
    {
        $tree = new Tree(
            [
                'label' => 'root',
                'children' => [
                    ['label' => 'child1'],
                    [
                        'label' => 'child2,',
                        'children' => [
                            [
                                'label' => 'child2.1',
                                'children' => [
                                    ['label' => 'child2.1.1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'child3',
                        'children' => [
                            ['label' => 'child3.1'],
                        ],
                    ],
                ],
            ],
            fn($node) => $node['label'],
            fn($node) => $node['children'] ?? [],
        );

        ob_start();

        service(TreePrinter::class)->print($tree, service(ConsolePrinter::class));

        $output = ob_get_clean();

        self::assertStringContainsString(
            "\e[38;5;75m │ \e[0m\e[38;5;75m   \e[0m\e[38;5;75m └─\e[0m\e[mchild2.1.1\e[0m",
            $output
        );
    }
}
