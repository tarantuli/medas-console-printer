<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class NullGlyph implements ConfigOption
{
    public function __construct(
        private Group $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
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
