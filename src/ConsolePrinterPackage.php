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
        new BinFileChecker()->check();
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
