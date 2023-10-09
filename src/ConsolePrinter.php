<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\Format, Printable, Printer, Table, Text};
use Medas\ConsolePrinter\{ConfigOptions\NullGlyph,
    Exceptions\NoPrintingImplementationForBlockType,
    Printer\BashFormat,
    Printer\Table\TablePrinter};
use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
class ConsolePrinter implements Printer
{
    public function __construct(
        #[ConfigValue(NullGlyph::class)]
        private readonly string       $nullGlyph,
        private readonly TablePrinter $tablePrinter,
        private readonly BashFormat   $bashFormat,
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

    private function printBlock(Printable|null $block): void
    {
        if ($block === null) {
            echo $this->nullGlyph;
            return;
        }

        if ($block instanceof Text) {
            if ($block->format === null) {
                echo $block->text;
            }
            else {
                $this->format($block->text, $block->format);
            }

            return;
        }

        if ($block instanceof Table) {
            $this->tablePrinter->print($block);

            return;
        }

        throw new NoPrintingImplementationForBlockType($block);
    }

    /** @param Format|Format[] $formats */
    private function format(string $string, mixed $formats): void
    {
        $codes = [];

        foreach (is_array($formats) ? $formats : [$formats] as $format) {
            $codes[] = $this->bashFormat->getCode($format);
        }

        printf("\e[%sm%s\e[0m", implode(';', $codes), $string);
    }
}
