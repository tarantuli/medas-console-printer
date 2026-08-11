<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands\ArgumentParser;

/**
 * The result of parsing raw command-line parts: the positional values (not yet mapped
 * onto the command's named Arguments) and the recognized options. Sits between
 * ArgumentParser::parse() and ArgumentParser::validate() - the seam that lets --help be
 * handled before the arguments are validated.
 */
readonly class ParsedInput
{
    public function __construct(
        /** @var string[] */
        public array $values,

        /** @var array<string, string|bool> */
        public array $options,
    )
    {
    }

    public function getOption(string $name): string|bool|null
    {
        return $this->options[$name] ?? null;
    }
}
