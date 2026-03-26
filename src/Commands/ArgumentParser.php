<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\Commands\{Arguments, ConsoleCommand, Option};
use Medas\Core\Attributes\Service;

#[Service]
readonly class ArgumentParser
{
    public function parse(ConsoleCommand $command, array $parts): Arguments
    {
        // The first argument is always the command name, ignore it.
        array_shift($parts);

        [$arguments, $options] = $this->processParts($parts);

        $this->checkArgumentCount($command, $arguments);
        $this->checkOptions($command, $options);

        return new Arguments($arguments, $options);
    }

    private function processParts(array $parts): array
    {
        $afterDoubleDash = false;
        $arguments = [];
        $options = [];

        foreach ($parts as $part) {
            if ($afterDoubleDash) {
                $arguments[] = $part;

                continue;
            }

            if ($part === '--') {
                $afterDoubleDash = true;

                continue;
            }

            if (str_starts_with($part, '--')) {
                $option = substr($part, 2);
                [$name, $value] = explode('=', $option, 2);
                $options[$name] = $value ?? null;

                continue;
            }

            if (str_starts_with($part, '-')) {
                $option = substr($part, 1);
                [$name, $value] = explode('=', $option, 2);

                if (strlen($name) > 1) {
                    if ($value !== null) {
                        throw new Exceptions\MultipleShortCodesWithValueGiven($name, $value);
                    }

                    $letters = explode('', $name);

                    foreach ($letters as $letter) {
                        $options[$letter] = null;
                    }
                }
                else {
                    $options[$name] = $value ?? null;
                }

                continue;
            }

            $arguments[] = $part;
        }

        return [$arguments, $options];
    }

    private function checkArgumentCount(ConsoleCommand $command, Arguments $arguments): void
    {
        $count = count($arguments->arguments);

        if ($count < $command->minArgumentCount()) {
            throw new Exceptions\TooFewArgumentsGiven($count, $command->minArgumentCount());
        }

        if ($count > $command->maxArgumentCount()) {
            throw new Exceptions\TooManyArgumentsGiven($count, $command->maxArgumentCount());
        }
    }

    private function checkOptions(ConsoleCommand $command, array $options): void
    {
        foreach ($options as $name => $value) {
            $option = array_find(
                $command->options(),
                fn(Option $option) => $option->longCode === $name || $option->shortCode === $name
            );

            if ($option === null) {
                throw new Exceptions\UnknownOptionGiven($name);
            }

            if ($option->valueRequired && $value === null) {
                throw new Exceptions\RequiredOptionValueNotGiven($name);
            }
        }
    }
}
