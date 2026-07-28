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

        foreach ($definitions as $definition) {
            if ($definition->isVariadic) {
                $remaining = array_slice($values, $index);
                $index = $count;

                if ($definition->required && $remaining === []) {
                    throw new TooFewArgumentsGiven(
                        $count,
                        $this->minimumArgumentCount($definitions)
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
                throw new TooFewArgumentsGiven($count, $this->minimumArgumentCount($definitions));
            }

            $resolved[$definition->name] = $definition->default;
        }

        // Any positional values left over means more were given than declared. This can't happen when the
        // last Argument is variadic, since that branch always consumes everything remaining.
        if ($index < $count) {
            throw new TooManyArgumentsGiven($count, count($definitions));
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
}
