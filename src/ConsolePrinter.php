<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Blocks, Diff, Printable, Printer, Table, Text};
use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
readonly class ConsolePrinter implements Printer
{
    public function __construct(
        #[ConfigValue(ConfigOptions\NullGlyph::class)]
        private string              $nullGlyph,
        private Diffs\DiffPrinter   $diffPrinter,
        private Tables\TablePrinter $tablePrinter,
        private Texts\TextPrinter   $textPrinter,
    )
    {
    }

    public function print(Printable ...$blocks): Printer
    {
        foreach ($blocks as $block) {
            $this->printBlock($block);
        }

        return $this;
    }

    private function printBlock(Printable|null $block): void
    {
        if ($block === null) {
            echo $this->nullGlyph;

            return;
        }

        if ($block instanceof Text) {
            $this->textPrinter->print($block);

            return;
        }

        if ($block instanceof Table) {
            $this->tablePrinter->print($block);

            return;
        }

        if ($block instanceof Diff) {
            $this->diffPrinter->print($block);

            return;
        }

        if ($block instanceof Blocks) {
            foreach ($block->blocks as $subBlock) {
                $this->printBlock($subBlock);
            }

            return;
        }

        throw new Exceptions\NoPrintingImplementationForBlockType($block);
    }

    public function printLine(Printable ...$blocks): self
    {
        $this->print(...$blocks);

        echo "\n";

        return $this;
    }

    public function printText(string $text, mixed $format = null): Printer
    {
        $this->print(Text::create($text, $format));

        return $this;
    }

    public function printTextLine(string $text, mixed $format = null): Printer
    {
        $this->print(Text::create($text, $format));

        echo "\n";

        return $this;
    }

    public function printEol(): Printer
    {
        echo "\n";

        return $this;
    }
}
