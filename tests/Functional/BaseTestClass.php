<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\Functional;

use Medas\ConfigManager\ConfigManager;
use Medas\ConsolePrinterTest\MockUps\MockUpPackage;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    public function execute(string $command): array
    {
        $command .= ' --addPackage=' . escapeshellarg(MockUpPackage::class);
        $pathToPhp = service(ConfigManager::class)->getValue('console.path-to-php');
        $pathToConsole = realpath(__DIR__ . '/../../bin/console');

        $string = '"' . $pathToPhp . '" ' . $pathToConsole . ' ' . $command;
        exec($string, $output);

        return $output;
    }
}
