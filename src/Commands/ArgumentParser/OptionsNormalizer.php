<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands\ArgumentParser;

use Medas\Console\Commands\{ConsoleCommand, Option};
use Medas\ConsolePrinter\Exceptions\{
    DisallowedOptionValueGiven,
    RequiredOptionValueNotGiven,
    UnknownOptionGiven
};
use Medas\Core\Attributes\Service;

#[Service]
readonly class OptionsNormalizer
{
    private array $sharedOptions;

    public function __construct()
    {
        $this->sharedOptions = [
            Option::noValue('help', '?'),
            Option::noValue('debug'),
        ];
    }

    public function normalize(ConsoleCommand $command, array $options): array
    {
        $normalizedOptions = [];
        $availableOptions = array_merge($this->sharedOptions, $command->options());

        foreach ($options as $name => $value) {
            $option = array_find(
                $availableOptions,
                fn(Option $option) => $option->longCode === $name || $option->shortCode === $name
            );

            if ($option === null) {
                throw new UnknownOptionGiven($name);
            }

            if ($option->valueRequired && $value === true) {
                throw new RequiredOptionValueNotGiven($name);
            }

            if (!$option->valueAllowed && $value !== true) {
                throw new DisallowedOptionValueGiven($name, $value);
            }

            // Always use the long code name for the option.
            $normalizedOptions[$option->longCode] = $value;
        }

        return $normalizedOptions;
    }
}
