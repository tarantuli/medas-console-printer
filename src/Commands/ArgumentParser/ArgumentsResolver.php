<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands\ArgumentParser;

use Medas\Console\Commands\{Argument, ConsoleCommand};
use Medas\ConsolePrinter\Exceptions\{
    InvalidArgumentValueGiven,
    TooFewArgumentsGiven,
    TooManyArgumentsGiven
};
use Medas\Core\Attributes\Service;

#[Service]
readonly class ArgumentsResolver
{
    /**
     * Maps the positional values given on the command line onto the named Arguments declared by the command
     * via ConsoleCommand::arguments(), in order. Any Argument not supplied a value falls back to its
     * `default`. Along the way, this validates that enough (and not too many) values were given, and runs
     * each Argument's `validator` against the value(s) it received.
     *
     * @param string[] $values
     * @return array<string, mixed> keyed by Argument::$name, ready to hand to CommandInput unchanged
     */
    public function resolve(ConsoleCommand $command, array $values): array
    {
        $definitions = $command->arguments();
        $resolved = [];
        $index = 0;
        $count = count($values);

        foreach ($definitions as $definitionIndex => $definition) {
            if ($definition->isVariadic) {
                $remaining = array_slice($values, $index);
                $index = $count;

                if ($definition->required && $remaining === []) {
                    throw new TooFewArgumentsGiven(
                        $count,
                        $this->minimumArgumentCount($definitions),
                        $this->missingRequiredArguments($definitions, $definitionIndex)
                    );
                }

                foreach ($remaining as $value) {
                    $this->validateValue($definition, $value);
                }

                $resolved[$definition->name] = $remaining !== []
                    ? $remaining
                    : $definition->default;

                continue;
            }

            if ($index < $count) {
                $value = $values[$index++];

                $this->validateValue($definition, $value);

                $resolved[$definition->name] = $value;

                continue;
            }

            if ($definition->required) {
                throw new TooFewArgumentsGiven(
                    $count,
                    $this->minimumArgumentCount($definitions),
                    $this->missingRequiredArguments($definitions, $definitionIndex)
                );
            }

            $resolved[$definition->name] = $definition->default;
        }

        // Any positional values left over means more were given than declared. This can't happen when the
        // last Argument is variadic, since that branch always consumes everything remaining.
        if ($index < $count) {
            throw new TooManyArgumentsGiven(
                $count,
                count($definitions),
                array_slice($values, $index)
            );
        }

        return $resolved;
    }

    private function validateValue(Argument $definition, string $value): void
    {
        if ($definition->validator !== null && !$definition->validator->isValid($value)) {
            throw new InvalidArgumentValueGiven($definition->name, $value);
        }
    }

    /** @param Argument[] $definitions */
    private function minimumArgumentCount(array $definitions): int
    {
        $min = 0;

        foreach ($definitions as $definition) {
            if ($definition->required) {
                $min++;
            }
        }

        return $min;
    }

    /**
     * The names of the required Arguments from $fromIndex onward - the ones still awaiting a value at the
     * point too few were given. Values are exhausted by then, so every required Argument from here is missing.
     *
     * @param Argument[] $definitions
     * @return string[]
     */
    private function missingRequiredArguments(array $definitions, int $fromIndex): array
    {
        $missing = [];

        foreach (array_slice($definitions, $fromIndex) as $definition) {
            if ($definition->required) {
                $missing[] = $definition->name;
            }
        }

        return $missing;
    }
}
