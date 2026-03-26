<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Core\{AsSingleton, BasePackage};

class ConsolePrinterPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [];
    }

    public function postInstall(): void
    {
        $thisConsole = file_get_contents(__DIR__ . '/../bin/console');
        $projectConsole = file_get_contents(getcwd() . '/bin/console');

        if ($thisConsole !== $projectConsole) {
            echo "The content at /bin/console is different from the one in this package.\n";
        }
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
