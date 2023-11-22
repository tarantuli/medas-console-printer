<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\Color, Printer, Text};
use Medas\Core\{Attributes\Service, Exceptions\Suggestions, StringMaker};

#[Service]
class ExceptionPrinter
{
    private Printer|null $printer = null;
    private \Throwable $exception;

    public function print(\Throwable $exception): void
    {
        $this->exception = $exception;

        try {
            $this->loadPrinter();
            $this->printVerboseExceptionInformation();
        }
        catch (\Throwable) {
            // Last ditch effort to print something useful
            $this->printAnyExceptionInformation();
        }
    }

    private function loadPrinter(): void
    {
        if ($this->printer === null) {
            // Fetch the service as late as possible, here, and not in the constructor through injection.
            // The printer itself could be the cause of the exception.
            $this->printer = service(Printer::class);
        }
    }

    private function printVerboseExceptionInformation(): void
    {
        $this->printBacktrace();
        $this->printExceptionInformation();
        $this->printSuggestions();
    }

    private function printBacktrace(): void
    {
        foreach (array_reverse($this->exception->getTrace()) as $trace) {
            if (isset($trace['file'])) {
                $this->printer->printLine(new Text($trace['file'] . ':' . $trace['line'], Color::LightGray));
            }

            $this->printer->printLine(new Text(' ' . (
                isset($trace['class'])
                ? $trace['class'] . $trace['type']
                : ''
            ) . $trace['function'] . '()', Color::LightYellow));

            foreach ($trace['args'] as $i => $argument) {
                $this->printer->printLine(
                    new Text('   ' . $i, Color::Cyan),
                    new Text('  ' . StringMaker::instance()->fromVariable($argument, true, true)),
                );
            }

            $this->printer->printLine();
        }
    }

    private function printExceptionInformation(): void
    {
        $this->printer
            ->printLine(new Text($this->exception->getFile() . ':' . $this->exception->getLine(), Color::LightGray))
            ->printLine(new Text(' Exception: ' . $this->exception::class, Color::LightYellow))
            ->printLine(new Text('   >', Color::Cyan), new Text('  ' . $this->exception->getMessage()))
            ->printLine();
    }

    private function printSuggestions(): void
    {
        if (!$this->exception instanceof Suggestions) {
            return;
        }

        $this->printer->printLine(new Text(' Suggestions:', Color::LightYellow));

        foreach ($this->exception->suggestions() as $key => $value) {
            if (is_string($key)) {
                $suggestion = $key;
                $indentation = $value;
            }
            else {
                $suggestion = $value;
                $indentation = 0;
            }

            $prefix = '   ' . str_repeat('   ', $indentation) . '>  ';

            $this->printer
                ->printLine(new Text($prefix, Color::Cyan), new Text($suggestion));
        }

        $this->printer->printLine();
    }

    private function printAnyExceptionInformation(): void
    {
        echo $this->exception->getFile(),
            ':',
            $this->exception->getLine(),
            ' ',
            $this->exception->getMessage(),
            "\n";
    }
}
