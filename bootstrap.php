<?php

declare(strict_types=1);

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Console\ConsolePackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ObjectInstantiator\{ObjectInstantiator, ObjectInstantiatorPackage};
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(ObjectInstantiator::class);

    $config->addPackages([
        ConfigOptionsPackage::instance(),
        ConsolePackage::instance(),
        ConsolePrinterPackage::instance(),
        ObjectInstantiatorPackage::instance(),
    ]);

    return $config;
});
