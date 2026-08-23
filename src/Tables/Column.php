<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables;

use Medas\Console\{Align, ColumnDef};

class Column
{
    public function __construct(
        public string         $header,
        public int            $width,
        public Align          $align = Align::Left,
        public bool           $truncate = false,

        /**
         * The declarative column this was built from, or null for a plain string header.
         * Presence of a `ColumnDef` is what switches this column from the legacy auto-align/
         * never-truncate behavior to the explicit width/align/truncate/style it declares.
         */
        public ColumnDef|null $columnDef = null,
    )
    {
    }
}
