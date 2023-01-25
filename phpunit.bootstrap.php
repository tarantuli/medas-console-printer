<?php

declare(strict_types=1);

use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ConsolePrinterTest\MockUps\MockUpPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

ServiceManager::get()
    ->addPackages([
        ConsolePrinterPackage::instance(),
        MockUpPackage::instance(),
    ]);
