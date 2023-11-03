<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables;

class Column
{
    public function __construct(
        public string $header,
        public int    $maxWidth
    )
    {
    }
}
