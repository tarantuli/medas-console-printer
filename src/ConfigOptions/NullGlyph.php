<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\ConfigOptions;

use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};
use Medas\ServiceManager\AsSingleton;

class NullGlyph implements ConfigOption
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return Group::instance();
    }

    public function name(): string
    {
        return 'null-glyph';
    }

    public function description(): string
    {
        return 'How to represent null values when printing';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return '〜';
    }
}
