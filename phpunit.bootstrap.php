<?php

declare(strict_types=1);

use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ConsolePrinterTest\MockUps\MockUpPackage;
use Medas\ObjectInstantiator\{ObjectInstantiator, ObjectInstantiatorPackage};
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(ObjectInstantiator::class);

    $config->addPackages([
        ConsolePrinterPackage::instance(),
        MockUpPackage::instance(),
        ObjectInstantiatorPackage::instance(),
    ]);

    return $config;
});
