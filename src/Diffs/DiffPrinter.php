<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Diffs;

use Medas\Console\{Diff, Formats\SafeColor, Text};
use Medas\ConsolePrinter\ConsolePrinter;
use Medas\Core\Attributes\Service;

#[Service]
readonly class DiffPrinter
{
    public function print(Diff $diff, ConsolePrinter $printer): void
    {
        foreach (explode("\n", $diff->output) as $line) {
            if (in_array(substr($line, 0, 3), ['+++', '---'])) {
                continue;
            }

            $type = substr($line, 0, 1);
            $color = match ($type) {
                '+' => SafeColor::Green,
                '-' => SafeColor::Red,
                '@' => SafeColor::Blue,
                '', ' ' => SafeColor::Gray,
                default => SafeColor::White,
            };

            $printer->printLine(Text::create($line, $color));
        }
    }
}
