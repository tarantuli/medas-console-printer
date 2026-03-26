<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables;

use Medas\Console\{Table, Text};
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
        $maxWidths = array_fill(0, $headerCount, 0);

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
                if ($value instanceof Text) {
                    $value = $value->text;
                }
                elseif (!is_string($value)) {
                    $value = StringMaker::instance()->fromVariable(
                        $value,
                        StringMaker\Settings::forDisplay()
                    );
                }

                $maxWidths[$i] = max($maxWidths[$i], mb_strlen($value));
            }
        }

        $job->columns = [];

        foreach ($table->headers as $i => $header) {
            $maxWidths[$i] = max($maxWidths[$i], mb_strlen($header));
            $job->columns[] = new Column($header, $maxWidths[$i]);
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
                $this->padString($column->header, $column->maxWidth),
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

            $elements[] = new Text(str_repeat('─', $column->maxWidth), $job->settings->lineColor);
        }

        $job->printer->printLine(...$elements);
    }

    private function printRecord(TablePrinter\Job $job, array $record): void
    {
        $elements = $this->initializeElements();

        foreach ($record as $i => $value) {
            if ($value === null) {
                $value = $this->nullGlyph;
            }

            if ($i > 0) {
                $elements[] = new Text(
                    str_repeat(' ', $this->columnSeparator),
                    $job->settings->lineColor
                );
            }

            if ($value instanceof Text) {
                $elements[] = new Text(
                    $this->padString($value->text, $job->columns[$i]->maxWidth),
                    ...$value->format
                );
            }
            else {
                if (!is_string($value)) {
                    $value = StringMaker::instance()->fromVariable(
                        $value,
                        StringMaker\Settings::forDisplay()
                    );
                }

                $elements[] = new Text($this->padString($value, $job->columns[$i]->maxWidth));
            }
        }

        $job->printer->printLine(...$elements);
    }

    private function initializeElements(): array
    {
        return [new Text(str_repeat(' ', $this->leftIndent))];
    }

    private function padString(string $value, int $width): string
    {
        $padLength = $width - mb_strwidth($value);

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
}
