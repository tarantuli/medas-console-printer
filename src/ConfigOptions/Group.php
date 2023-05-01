<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\ConfigOptions;

use Medas\Core\Interfaces\ConfigGroup;
use Medas\Core\AsSingleton;

class Group implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'console';
    }
}
