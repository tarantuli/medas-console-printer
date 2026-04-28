<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\ConsolePackage;
use Medas\Core\{AsSingleton, BasePackage};

class ConsolePrinterPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ConsolePackage::instance(),
        ];
    }

    public function postInstall(): void
    {
        $thisConsole = file_get_contents(__DIR__ . '/../bin/console');
        $projectConsole = file_get_contents(getcwd() . '/bin/console');

        if ($projectConsole && $thisConsole !== $projectConsole) {
            printf("The content at /bin/console is different from the one in %s.\n", getcwd());
        }
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
