<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables;

use Medas\Console\{Formats\Format, Formats\HexColor, Formats\Style, Table, Text};
use Medas\ConsolePrinter\{ConfigOptions\NullGlyph, ConsolePrinter};
use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\Core\StringMaker;

/**
 * @see https://en.wikipedia.org/wiki/Box-drawing_character#Box_Drawing for box drawing characters
 */
#[Service]
class TablePrinter
{
    private ConsolePrinter $printer;
    private Format $lineColor;
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
        $this->lineColor = new HexColor('#005f00');
    }

    public function print(Table $table): void
    {
        $this->columns = $this->getColumns($table);
        $this->printer = service(ConsolePrinter::class);

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
                if (!is_string($value)) {
                    $value = StringMaker::instance()->fromVariable($value, true, true);
                }

                $maxWidths[$i] = max($maxWidths[$i], mb_strlen($value));
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

        $this->printer->printLine(...$elements);
    }

    private function initializeElements(): array
    {
        return [new Text(str_repeat(' ', $this->leftIndent))];
    }

    private function padString(mixed $value, int $width): string
    {
        $padLength = $width - mb_strwidth((string) $value);

        if ($padLength <= 0) {
            return $value;
        }

        if (is_numeric($value)) {
            return str_repeat(' ', $padLength) . $value;
        }
        else {
            return $value . str_repeat(' ', $padLength);
        }
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

        $this->printer->printLine(...$elements);
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

            if (!is_string($value)) {
                $value = StringMaker::instance()->fromVariable($value, true, true);
            }

            $elements[] = new Text($this->padString($value, $this->columns[$i]->maxWidth));
        }

        $this->printer->printLine(...$elements);
    }
}
