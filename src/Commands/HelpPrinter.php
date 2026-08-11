<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands;

use Medas\Console\{Commands\ConsoleCommand, Formats\SafeColor};
use Medas\ConsolePrinter\ConsolePrinter;
use Medas\Core\Attributes\Service;

#[Service]
readonly class HelpPrinter
{
    public function __construct(
        private ConsolePrinter $printer,
    )
    {
    }

    public function print(ConsoleCommand $command): void
    {
        $this->printer
            ->printEol()
            ->printText($command->description(), SafeColor::DarkYellow)
            ->printEol()->printEol();

        if ($command->arguments()) {
            $this->printer
                ->printText('Arguments:', SafeColor::Green)
                ->printEol();

            foreach ($command->arguments() as $argument) {
                $declaration = $argument->required ? $argument->name : '[' . $argument->name . ']';

                $this->printer
                    ->printText('   ' . str_pad($declaration, 16), SafeColor::Yellow)
                    ->printText('   ' . $argument->description, SafeColor::Green)
                    ->printEol();
            }

            $this->printer->printEol()->printEol();
        }

        if ($command->options()) {
            $this->printer
                ->printText('Options:', SafeColor::Green)
                ->printEol();

            foreach ($command->options() as $option) {
                if ($option->shortCode) {
                    $code = '(--' . $option->longCode . '|-' . $option->shortCode . ')';
                }
                else {
                    $code = '--' . $option->longCode;
                }

                if ($option->valueRequired) {
                    $code .= '=VALUE';
                }
                elseif ($option->valueAllowed) {
                    $code .= '[=VALUE]';
                }

                $this->printer
                    ->printText('   ' . str_pad($code, 16), SafeColor::Yellow)
                    ->printText('   ' . $option->description, SafeColor::Green);
            }

            $this->printer->printEol()->printEol();
        }
    }
}
