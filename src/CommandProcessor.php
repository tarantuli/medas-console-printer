<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class CommandProcessor
{
    public function __construct(
        private readonly CommandFinder $commandFinder,
    )
    {
    }

    public function process(array $arguments): void
    {
        $processor = $this->commandFinder->find($arguments[0] ?? 'console:command-list');

        $processor->process($arguments);
    }
}
