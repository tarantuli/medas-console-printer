<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\Color, Printer, Text};
use Medas\Core\StringMaker;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ExceptionPrinter
{
    private Printer|null $printer = null;
    private \Error|\Exception $exception;

    public function print(\Exception|\Error $exception): void
    {
        $this->exception = $exception;

        try {
            $this->loadPrinter();
            $this->printVerboseExceptionInformation();
        }
        catch (\Exception|\Error) {
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
    }

    private function printBacktrace(): void
    {
        foreach (array_reverse($this->exception->getTrace()) as $trace) {
            if (isset($trace['file'])) {
                $this->printer->print(new Text(
                    $trace['file'] . ':' . $trace['line'],
                    Color::LightGray
                ));
            }

            $this->printer->print(new Text(
                ' ' . (isset($trace['class']) ? $trace['class'] . $trace['type'] : '') . $trace['function'] . '()',
                Color::LightYellow
            ));

            foreach ($trace['args'] as $i => $argument) {
                $this->printer->print(
                    new Text('   ' . $i, Color::Cyan),
                    new Text('  ' . StringMaker::fromVariable($argument)),
                );
            }

            $this->printer->print();
        }
    }

    private function printExceptionInformation(): void
    {
        $this->printer
            ->print(new Text(
                $this->exception->getFile() . ':' . $this->exception->getLine(),
                Color::LightGray
            ))
            ->print(new Text('  Exception [' . $this->exception::class . ']:', Color::LightYellow))
            ->print(new Text('    ' . $this->exception->getMessage()))
            ->print();
    }

    private function printAnyExceptionInformation(): void
    {
        echo $this->exception->getFile(), ':', $this->exception->getLine(), '  ', $this->exception->getMessage(), "\n";
    }
}
