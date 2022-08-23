<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\Functional;

use Medas\ConfigManager\ConfigManager;
use Medas\ConsolePrinterTest\MockUps\MockUpPackage;
use Medas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function execute(string $command): array
    {
        $command .= ' --addPackage=' . escapeshellarg(MockUpPackage::class);
        $pathToPhp = service(ConfigManager::class)->getValue('console.path-to-php');
        $pathToConsole = realpath(__DIR__ . '/../../bin/console');

        exec($pathToPhp . ' ' . $pathToConsole . ' ' . $command, $output);

        return $output;
    }

    protected function loadMockUps(): void
    {
        ServiceManager::get()->addPackage(MockUpPackage::instance());
    }
}
