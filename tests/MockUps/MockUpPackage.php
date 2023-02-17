<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\MockUps;

use Medas\ConfigManager\{ConfigManager, ConfigManagerPackage};
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage, ServiceConfig};

class MockUpPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ConfigManagerPackage::instance(),
            ConfigOptionsPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfig $config): void
    {
        $configManager = service(ConfigManager::class);

        $configManager->addDirectory(__DIR__);
        $configManager->readEnv(__DIR__ . '/../..');

        parent::initialize($config);
    }
}
