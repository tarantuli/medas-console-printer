<?php

declare(strict_types=1);

chdir(__DIR__);

ServiceManager::get()
    ->addPackage(PlaceholderPackage::instance());
