<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\Format, Printable, Printer, Table, Text};
use Medas\ConsolePrinter\{ConfigOptions\NullGlyph, Printer\BashFormat, Printer\Table\TablePrinter};
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

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

    public function print(Printable ...$blocks): self
    {
        foreach ($blocks as $block) {
            if ($block === null) {
                echo $this->nullGlyph;
            }
            elseif ($block instanceof Text) {
                if ($block->format === null) {
                    echo $block->text;
                }
                else {
                    $this->format($block->text, $block->format);
                }
            }
            elseif ($block instanceof Table) {
                $this->tablePrinter->print($block);
            }
            else {
                throw new \Exception('unknown block type ' . $block::class);
            }
        }

        return $this;
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

    public function printText(string $text, mixed $format = null): Printer
    {
        $this->print(Text::create($text, $format));

        return $this;
    }

    public function printEol(): Printer
    {
        print("\n");

        return $this;
    }
}
