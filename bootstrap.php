<?php

declare(strict_types=1);

use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

ServiceManager::get()
    ->addPackage(ConsolePrinterPackage::instance());
