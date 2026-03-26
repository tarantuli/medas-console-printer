<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Core\Attributes\Service;

#[Service]
readonly class CommandProcessor
{
    private const string DEFAULT_COMMAND = 'console:command-list';

    public function __construct(
        private Commands\ArgumentParser $argumentParser,
        private Finder                  $commandFinder,
    )
    {
    }

    public function process(array $givenArguments): void
    {
        $command = $this->commandFinder->find($givenArguments[0] ?? self::DEFAULT_COMMAND);
        $arguments = $this->argumentParser->parse($command, $givenArguments);

        $command->process($arguments);
    }
}
