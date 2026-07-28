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

    public function parse(ConsoleCommand $command, array $parts): CommandInput
    {
        // The first argument is always the command name, ignore it.
        array_shift($parts);

        [$values, $options] = $this->partsProcessor->process($parts);
        $arguments = $this->argumentsResolver->resolve($command, $values);
        $options = $this->optionsNormalizer->normalize($command, $options);

        return new CommandInput($arguments, $options);
    }
}
