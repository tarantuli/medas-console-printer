<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Core\{
    Attributes\Service,
    Events\DebugInformationGatherer,
    Interfaces\Package,
    Interfaces\ServiceManager
};

#[Service]
readonly class CommandProcessor
{
    private const string DEFAULT_COMMAND = 'console:command-list';

    public function __construct(
        private Commands\ArgumentParser  $argumentParser,
        private Commands\Finder          $commandFinder,
        private DebugInformationGatherer $debugInformationGatherer,
        private ServiceManager           $serviceManager,
    )
    {
    }

    public function process(array $givenArguments): void
    {
        $this->addTestPackages();

        $command = $this->commandFinder->find($givenArguments[0] ?? self::DEFAULT_COMMAND);
        $arguments = $this->argumentParser->parse($command, $givenArguments);

        $command->process($arguments);

        if ($arguments->getOption('debug')) {
            echo "\n\n[Debug information]\n";

            foreach ($this->debugInformationGatherer->events as $event) {
                echo $event, "\n";
            }
        }
    }

    private function addTestPackages(): void
    {
        $testPackages = getenv('MEDAS_TEST_PACKAGES');

        if ($testPackages === false) {
            return;
        }

        foreach (explode(',', $testPackages) as $testPackage) {
            if (!class_exists($testPackage)) {
                continue;
            }

            /** @var class-string<Package> $testPackage */
            /** @var Package $instance */
            $instance = $testPackage::instance();

            $this->serviceManager->config()->addPackage($instance);
        }
    }
}
