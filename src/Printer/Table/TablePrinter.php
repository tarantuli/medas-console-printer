<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Printer\Table;

use Medas\Console\Formats\Color;
use Medas\Console\Formats\Format;
use Medas\Console\Formats\Style;
use Medas\Console\Table;
use Medas\Console\Text;
use Medas\ConsolePrinter\ConfigOptions\NullGlyph;
use Medas\ConsolePrinter\Printer;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

/**
 * @see https://en.wikipedia.org/wiki/Box-drawing_character#Box_Drawing for box drawing characters
 */
#[Service]
class TablePrinter
{
    private Printer $printer;

    //   Printer\BashFormat::COLOR256 . '22';
    private Format $lineColor = Color::LightBlue;
    private Format $headerColor = Style::Bold;

    private int $leftIndent = 3;
    private int $columnSeparator = 3;

    /** @var Column[] */
    private array $columns;

    public function __construct(
        #[ConfigValue(NullGlyph::class)]
        private readonly string $nullGlyph,
    )
    {
    }

    public function print(Table $table): void
    {
        $this->columns = $this->getColumns($table);
        $this->printer = service(Printer::class);

        $this->printHeader();
        $this->printHorizontalBorder();

        foreach ($table->data as $record) {
            $this->printRecord($record);
        }
    }

    /** @return Column[] */
    private function getColumns(Table $table): array
    {
        $maxWidths = array_fill(0, count($table->headers), 0);

        foreach ($table->data as $record) {
            foreach ($record as $i => $value) {
                $maxWidths[$i] = max($maxWidths[$i], mb_strlen((string) $value));
            }
        }

        $columns = [];
        foreach ($table->headers as $i => $header) {
            $maxWidths[$i] = max($maxWidths[$i], mb_strlen($header));
            $columns[] = new Column($header, $maxWidths[$i]);
        }

        return $columns;
    }

    private function printHeader(): void
    {
        $elements = $this->initializeElements();

        foreach ($this->columns as $i => $column) {
            if ($i > 0) {
                $elements[] = new Text(str_repeat(' ', $this->columnSeparator), $this->lineColor);
            }

            $elements[] = new Text($this->padString($column->header, $column->maxWidth), $this->headerColor);
        }

        $this->printer->print(...$elements);
    }

    private function padString(mixed $value, int $width): string
    {
        $padLength = $width - mb_strwidth((string) $value);

        if ($padLength <= 0) {
            return $value;
        }

        if (is_int($value)) {
            return str_repeat(' ', $padLength) . $value;
        }
        else {
            return $value . str_repeat(' ', $padLength);
        }
    }

    private function initializeElements(): array
    {
        return [new Text(str_repeat(' ', $this->leftIndent))];
    }

    private function printHorizontalBorder(): void
    {
        $elements = $this->initializeElements();

        foreach ($this->columns as $i => $column) {
            if ($i > 0) {
                $elements[] = new Text(str_repeat('─', $this->columnSeparator), $this->lineColor);
            }

            $elements[] = new Text(str_repeat('─', $column->maxWidth), $this->lineColor);
        }

        $this->printer->print(...$elements);
    }

    private function printRecord(mixed $record): void
    {
        $elements = $this->initializeElements();

        foreach ($record as $i => $value) {
            if ($value === null) {
                $value = $this->nullGlyph;
            }

            if ($i > 0) {
                $elements[] = new Text(str_repeat(' ', $this->columnSeparator), $this->lineColor);
            }

            $elements[] = new Text($this->padString($value, $this->columns[$i]->maxWidth));
        }

        $this->printer->print(...$elements);
    }
}
