<?php

declare(strict_types=1);

/**
 * This file is needed to test bin/console
 */

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Console\ConsolePackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ServiceManager\ServiceConfig;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        ConsolePrinterPackage::instance(),
        ConsolePackage::instance(),
        ConfigOptionsPackage::instance(),
    ]);

    return $config;
});
