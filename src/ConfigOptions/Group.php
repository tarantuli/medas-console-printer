<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ConfigGroup;

#[Service]
class Group implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'console';
    }
}
