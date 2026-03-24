<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class TableLeftIndent implements ConfigOption
{
    public function __construct(
        private ConsolePrinterConfigGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'table-left-indent';
    }

    public function description(): string
    {
        return 'Number of spaces to indent table rows from the left';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        return 3;
    }
}
