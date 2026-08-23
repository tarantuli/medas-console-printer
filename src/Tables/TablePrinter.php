<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables;

use Medas\Console\{Align, ColumnDef, Formats\Format, Style, Table, Text};
use Medas\ConsolePrinter\{
    ConfigOptions\NullGlyph,
    ConfigOptions\TableColumnSeparator,
    ConfigOptions\TableLeftIndent,
    ConsolePrinter,
    Exceptions\RecordAndHeaderHaveDifferentLengths
};
use Medas\Core\{Attributes\ConfigValue, Attributes\Service, StringMaker};

/**
 * @see https://en.wikipedia.org/wiki/Box-drawing_character#Box_Drawing for box drawing characters
 */
#[Service]
readonly class TablePrinter
{
    public function __construct(
        #[ConfigValue(NullGlyph::class)]
        private string $nullGlyph,

        #[ConfigValue(TableLeftIndent::class)]
        private int    $leftIndent,

        #[ConfigValue(TableColumnSeparator::class)]
        private int    $columnSeparator,
    )
    {
    }

    public function print(Table $table, ConsolePrinter $printer, TablePrinter\Settings|null $settings = null): void
    {
        $job = new TablePrinter\Job($table, $printer, $settings ?? new TablePrinter\Settings());

        $this->determineColumns($job, $table);
        $this->printHeader($job);
        $this->printHorizontalBorder($job);

        foreach ($table->data as $record) {
            $this->printRecord($job, array_values($record));
        }
    }

    private function determineColumns(TablePrinter\Job $job, Table $table): void
    {
        $headerCount = count($table->headers);
        $contentWidths = array_fill(0, $headerCount, 0);

        foreach ($table->data as $recordIndex => $record) {
            $record = array_values($record);

            if (count($record) !== $headerCount) {
                throw new RecordAndHeaderHaveDifferentLengths(
                    $recordIndex,
                    $record,
                    $table->headers
                );
            }

            foreach ($record as $i => $value) {
                $contentWidths[$i] = max($contentWidths[$i], mb_strwidth($this->cellText($value)));
            }
        }

        $job->columns = [];

        foreach ($table->headers as $i => $header) {
            if ($header instanceof ColumnDef) {
                $width = $header->width ?? max($contentWidths[$i], mb_strwidth($header->header));

                $job->columns[] = new Column(
                    $header->header,
                    $width,
                    $header->align,
                    $header->truncate,
                    $header,
                );

                continue;
            }

            $headerText = $header instanceof Text ? $header->text : (string) $header;
            $width = max($contentWidths[$i], mb_strwidth($headerText));
            $job->columns[] = new Column($headerText, $width);
        }
    }

    private function printHeader(TablePrinter\Job $job): void
    {
        $elements = $this->initializeElements();

        foreach ($job->columns as $i => $column) {
            if ($i > 0) {
                $elements[] = new Text(
                    str_repeat(' ', $this->columnSeparator),
                    $job->settings->lineColor
                );
            }

            $elements[] = new Text(
                $this->layout($column->header, $column),
                $job->settings->headerColor
            );
        }

        $job->printer->printLine(...$elements);
    }

    private function printHorizontalBorder(TablePrinter\Job $job): void
    {
        $elements = $this->initializeElements();

        foreach ($job->columns as $i => $column) {
            if ($i > 0) {
                $elements[] = new Text(
                    str_repeat('─', $this->columnSeparator),
                    $job->settings->lineColor
                );
            }

            $elements[] = new Text(str_repeat('─', $column->width), $job->settings->lineColor);
        }

        $job->printer->printLine(...$elements);
    }

    private function printRecord(TablePrinter\Job $job, array $record): void
    {
        $elements = $this->initializeElements();

        foreach ($record as $i => $value) {
            $column = $job->columns[$i];

            if ($i > 0) {
                $elements[] = new Text(
                    str_repeat(' ', $this->columnSeparator),
                    $job->settings->lineColor
                );
            }

            if ($value === null) {
                $elements[] = new Text($this->layout($this->nullGlyph, $column));

                continue;
            }

            if ($value instanceof Text) {
                // An already-styled cell always wins over any column/default style — this is the
                // existing behaviour (e.g. the per-status `Text::create('OK', SafeColor::Green)`
                // cells shown in this package's README), unchanged.
                $elements[] = new Text($this->layout($value->text, $column), ...$value->format);

                continue;
            }

            $text = $this->cellText($value);

            $cellStyle = $column->columnDef?->resolveStyle(
                $value,
                $record
            ) ?? $job->settings->defaultCellStyle;

            $elements[] = new Text(
                $this->layout($text, $column),
                ...$this->colorFormats($cellStyle)
            );
        }

        $job->printer->printLine(...$elements);
    }

    private function initializeElements(): array
    {
        return [new Text(str_repeat(' ', $this->leftIndent))];
    }

    private function cellText(mixed $value): string
    {
        if ($value instanceof Text) {
            return $value->text;
        }

        if (is_string($value)) {
            return $value;
        }

        return StringMaker::instance()->fromVariable($value, StringMaker\Settings::forDisplay());
    }

    /**
     * Pads/truncates/aligns a cell's text to its column's width. Columns built from a
     * {@see ColumnDef} use its explicit `align`/`truncate`; a plain string-header column keeps
     * the original behaviour exactly — auto-sized to content, right-aligned when the value is
     * numeric, never truncated.
     */
    private function layout(string $value, Column $column): string
    {
        if ($column->columnDef && $column->truncate && mb_strwidth($value) > $column->width) {
            $suffix = '…';
            $keep = max(0, $column->width - mb_strwidth($suffix));
            $value = mb_substr($value, 0, $keep) . $suffix;
        }

        $padLength = $column->width - mb_strwidth($value);

        if ($padLength <= 0) {
            return $value;
        }

        $align = $column->columnDef
            ? $column->align
            : (is_numeric($value) ? Align::Right : Align::Left);

        return match ($align) {
            Align::Right => str_repeat(' ', $padLength) . $value,

            Align::Center
                => str_repeat(' ', intdiv($padLength, 2))
                    . $value
                    . str_repeat(' ', $padLength - intdiv($padLength, 2)),

            Align::Left => $value . str_repeat(' ', $padLength),
        };
    }

    /** @return Format[] */
    private function colorFormats(Style|null $style): array
    {
        return $style?->colorFormats() ?? [];
    }
}
