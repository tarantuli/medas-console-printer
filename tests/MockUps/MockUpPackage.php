<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\MockUps;

use Medas\ConfigManager\{ConfigManager, ConfigManagerPackage};
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage};

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

    public function initialize(): void
    {
        $config = sm()->resolve(ConfigManager::class);

        $config->addDirectory(__DIR__);
        $config->readEnv(__DIR__ . '/../..');

        parent::initialize();
    }
}
