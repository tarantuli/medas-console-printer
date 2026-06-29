<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{Formats\SafeColor, Text};
use Medas\Core\{Attributes\Service, Events\DebugInformationGatherer};

#[Service]
readonly class CommandProcessor
{
    private const string DEFAULT_COMMAND = 'console:command-list';

    public function __construct(
        private Commands\ArgumentParser  $argumentParser,
        private Commands\Finder          $commandFinder,
        private ConsolePrinter           $printer,
        private DebugInformationGatherer $debugInformationGatherer,
    )
    {
    }

    public function process(array $givenArguments): void
    {
        try {
            $argument = $givenArguments[0] ?? self::DEFAULT_COMMAND;
            $command = $this->commandFinder->find($argument);
        }
        catch (Exceptions\FinderException $exception) {
            $this->printer->print(Text::create("Could not execute command \"$argument\":", SafeColor::Red));
            $this->printer->print(Text::create('   ' . $exception->getMessage(), SafeColor::Red));

            return;
        }

        $arguments = $this->argumentParser->parse($command, $givenArguments);

        $command->process($arguments);

        if ($arguments->getOption('debug')) {
            echo "\n\n[Debug information]\n";

            foreach ($this->debugInformationGatherer->events as $event) {
                echo $event, "\n";
            }
        }
    }
}
