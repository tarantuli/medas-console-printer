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
        $pathToPhp = service(ConfigManager::class)->getValue('console.path-to-php');
        $pathToConsole = realpath(__DIR__ . '/../../bin/medas');
        $string = '"' . $pathToPhp . '" ' . $pathToConsole . ' ' . $command;

        putenv('MEDAS_TEST_PACKAGES=' . MockUpPackage::class);

        exec($string, $output);

        putenv('MEDAS_TEST_PACKAGES=');

        return $output;
    }
}
