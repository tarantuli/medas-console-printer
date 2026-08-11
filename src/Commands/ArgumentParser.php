<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands;

use Medas\Console\Commands\{CommandInput, ConsoleCommand};
use Medas\Core\Attributes\Service;

#[Service]
readonly class ArgumentParser
{
    public function __construct(
        private ArgumentParser\ArgumentsResolver $argumentsResolver,
        private ArgumentParser\OptionsNormalizer $optionsNormalizer,
        private ArgumentParser\PartsProcessor    $partsProcessor,
    )
    {
    }

    public function parse(ConsoleCommand $command, array $parts): ArgumentParser\ParsedInput
    {
        // The first argument is always the command name, ignore it.
        array_shift($parts);

        [$values, $options] = $this->partsProcessor->process($parts);
        $options = $this->optionsNormalizer->normalize($command, $options);

        return new ArgumentParser\ParsedInput($values, $options);
    }

    public function validate(ConsoleCommand $command, ArgumentParser\ParsedInput $parsed): CommandInput
    {
        $arguments = $this->argumentsResolver->resolve($command, $parsed->values);

        return new CommandInput($arguments, $parsed->options);
    }
}
