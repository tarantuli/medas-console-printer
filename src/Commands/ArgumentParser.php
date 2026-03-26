<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands;

use Medas\Console\Commands\{CommandInput, ConsoleCommand, Option};
use Medas\ConsolePrinter\Exceptions;
use Medas\Core\Attributes\Service;

#[Service]
readonly class ArgumentParser
{
    public function parse(ConsoleCommand $command, array $parts): CommandInput
    {
        // The first argument is always the command name, ignore it.
        array_shift($parts);

        [$arguments, $options] = $this->processParts($parts);

        $this->checkArgumentCount($command, $arguments);

        $options = $this->normalizeOptions($command, $options);

        return new CommandInput($arguments, $options);
    }

    /**
     * @return array{0: string[], 1: array<string, string|bool>}
     */
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
                $options[$name] = $value ?? true;

                continue;
            }

            if (str_starts_with($part, '-')) {
                $option = substr($part, 1);
                [$name, $value] = explode('=', $option, 2);

                if (strlen($name) > 1) {
                    if ($value !== null) {
                        throw new Exceptions\MultipleShortCodesWithValueGiven($name, $value);
                    }

                    $letters = str_split($name);

                    foreach ($letters as $letter) {
                        $options[$letter] = true;
                    }
                }
                else {
                    $options[$name] = $value ?? true;
                }

                continue;
            }

            $arguments[] = $part;
        }

        return [$arguments, $options];
    }

    private function checkArgumentCount(ConsoleCommand $command, array $arguments): void
    {
        $count = count($arguments);
        $allowedArgumentCount = $command->allowedArgumentCount();

        if ($count < $allowedArgumentCount->min) {
            throw new Exceptions\TooFewArgumentsGiven($count, $allowedArgumentCount->min);
        }

        if ($allowedArgumentCount->max !== false && $count > $allowedArgumentCount->max) {
            throw new Exceptions\TooManyArgumentsGiven($count, $allowedArgumentCount->max);
        }
    }

    private function normalizeOptions(ConsoleCommand $command, array $options): array
    {
        $normalizedOptions = [];

        foreach ($options as $name => $value) {
            $option = array_find(
                $command->options(),
                fn(Option $option) => $option->longCode === $name || $option->shortCode === $name
            );

            if ($option === null) {
                throw new Exceptions\UnknownOptionGiven($name);
            }

            if ($option->valueRequired && $value === true) {
                throw new Exceptions\RequiredOptionValueNotGiven($name);
            }

            if (!$option->valueAllowed && $value !== true) {
                throw new Exceptions\DisallowedOptionValueGiven($name, $value);
            }

            // Always use the long code name for the option.
            $normalizedOptions[$option->longCode] = $value;
        }

        return $normalizedOptions;
    }
}
