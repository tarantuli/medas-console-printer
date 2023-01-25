<?php

declare(strict_types=1);

/**
 * This file is needed to test bin/console
 */

use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

require_once 'vendor/autoload.php';

ServiceManager::get()
    ->addPackage(ConsolePrinterPackage::instance());
