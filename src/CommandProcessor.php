<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Core\Attributes\Service;
use Medas\ServiceManager\Package;

#[Service]
class CommandProcessor
{
    private const string DEFAULT_COMMAND = 'console:command-list';

    public function __construct(
        private readonly CommandFinder    $commandFinder,
        private readonly ExceptionPrinter $exceptionPrinter,
    )
    {
    }

    public function process(array $arguments): void
    {
        $this->addRequestedPackages($arguments);

        try {
            $consoleCommand = $this->commandFinder->find($arguments[0] ?? self::DEFAULT_COMMAND);

            $consoleCommand->process($arguments);
        }
        catch (\Throwable $exception) {
            $this->exceptionPrinter->print($exception);
        }
    }

    private function addRequestedPackages(array $arguments): void
    {
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--addPackage=')) {
                /** @var Package $class */
                $class = substr($argument, 13);

                /** @noinspection PhpParamsInspection */
                sm()->config()->addPackage($class::instance());
            }
        }
    }
}
