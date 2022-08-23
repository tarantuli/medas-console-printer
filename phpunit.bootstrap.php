<?php

declare(strict_types=1);

use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

ServiceManager::get()
    ->addPackage(ConsolePrinterPackage::instance());
