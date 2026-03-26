<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\Color, Printer, Text};
use Medas\Core\{Attributes\Service, Exceptions\Suggestions, StringMaker};

#[Service]
readonly class ExceptionPrinter
{
    public function print(\Throwable $exception): void
    {
        try {
            // Fetch the service as late as possible, here, and not in the constructor through injection.
            // The printer itself could be the cause of the exception.
            $printer = service(Printer::class);

            $this->printVerboseExceptionInformation($exception, $printer);
        }
        catch (\Throwable) {
            // Last ditch effort to print something useful
            $this->printAnyExceptionInformation($exception);
        }
    }

    private function printVerboseExceptionInformation(\Throwable $exception, Printer $printer): void
    {
        $this->printBacktrace($exception, $printer);
        $this->printExceptionInformation($exception, $printer);
        $this->printSuggestions($exception, $printer);
    }

    private function printBacktrace(\Throwable $exception, Printer $printer): void
    {
        foreach (array_reverse($exception->getTrace()) as $trace) {
            if (isset($trace['file'])) {
                $printer->printLine(new Text($trace['file'] . ':' . $trace['line'], Color::LightGray));
            }

            $printer->printLine(new Text(' ' . (
                isset($trace['class'])
                ? $trace['class'] . $trace['type']
                : ''
            ) . $trace['function'] . '()', Color::LightYellow));

            foreach ($trace['args'] ?? [] as $i => $argument) {
                $printer->printLine(
                    new Text('   ' . $i, Color::Cyan),
                    new Text('  ' . StringMaker::instance()->fromVariable($argument, StringMaker\Settings::forDisplay())),
                );
            }

            $printer->printLine();
        }
    }

    private function printExceptionInformation(\Throwable $exception, Printer $printer): void
    {
        $printer
            ->printLine(new Text($exception->getFile() . ':' . $exception->getLine(), Color::LightGray))
            ->printLine(new Text(' Exception: ' . $exception::class, Color::LightYellow))
            ->printLine(new Text('   >', Color::Cyan), new Text('  ' . $exception->getMessage()))
            ->printLine();
    }

    private function printSuggestions(\Throwable $exception, Printer $printer): void
    {
        if (!$exception instanceof Suggestions) {
            return;
        }

        $printer->printLine(new Text(' Suggestions:', Color::LightYellow));

        foreach ($exception->suggestions() as $key => $value) {
            if (is_string($key)) {
                $suggestion = $key;
                $indentation = (int) $value;
            }
            else {
                $suggestion = $value;
                $indentation = 0;
            }

            $prefix = '   ' . str_repeat('   ', $indentation) . '>  ';

            $printer
                ->printLine(new Text($prefix, Color::Cyan), new Text($suggestion));
        }

        $printer->printLine();
    }

    private function printAnyExceptionInformation(\Throwable $exception): void
    {
        echo $exception->getFile(), ':', $exception->getLine(), ' ', $exception->getMessage(), "\n";
    }
}
