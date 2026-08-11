<?php

declare(strict_types=1);

use Medas\ConsolePrinter\ConsolePrinter;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ConsolePrinter\Tables\TablePrinter;
use Medas\ObjectInstantiator\{ObjectInstantiator, ObjectInstantiatorPackage};
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

chdir(__DIR__);

require_once 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        ConsolePrinterPackage::instance(),
        ObjectInstantiatorPackage::instance(),
    ]);

    $config->addManualBinding(TablePrinter::class, 'nullGlyph', '--');
    $config->addManualBinding(TablePrinter::class, 'leftIndent', 3);
    $config->addManualBinding(TablePrinter::class, 'columnSeparator', 1);
    $config->addManualBinding(ConsolePrinter::class, 'nullGlyph', '--');

    return $config;
});
