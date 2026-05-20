<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Trees;

use Medas\Console\{Formats\Format, Formats\SafeColor, Tree};
use Medas\ConsolePrinter\ConsolePrinter;
use Medas\Core\Attributes\Service;

#[Service]
readonly class TreePrinter
{
    private Format $boxDrawingColor;

    public function __construct()
    {
        $this->boxDrawingColor = SafeColor::LightBlue;
    }

    public function print(Tree $tree, ConsolePrinter $printer): void
    {
        $this->printLevel($tree->nodes, $tree->label, $tree->children, $printer);
    }

    private function printLevel(
        iterable       $nodes,
        \Closure       $label,
        \Closure       $children,
        ConsolePrinter $printer,
        array          $levels = []
    ): void
    {
        $levelCount = count($levels);

        foreach ($levels as $j => $level) {
            if ($level) {
                $printer->printText(
                    $j === $levelCount - 1 ? ' └─' : '   ',
                    $this->boxDrawingColor
                );
            }
            else {
                $printer->printText(
                    $j === $levelCount - 1 ? ' ├─' : ' │ ',
                    $this->boxDrawingColor
                );
            }
        }

        $printer->printText($label($nodes));
        $printer->printEol();

        $childCount = count($children($nodes));

        foreach ($children($nodes) as $i => $child) {
            $this->printLevel(
                $child,
                $label,
                $children,
                $printer,
                array_merge($levels, [$i === $childCount - 1])
            );
        }
    }
}
