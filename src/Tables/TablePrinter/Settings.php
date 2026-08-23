<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables\TablePrinter;

use Medas\Console\{Formats\Decoration, Formats\Format, Formats\HexColor, Style};

class Settings
{
    public function __construct(
        public Format     $lineColor = new HexColor('#005f00'),
        public Format     $headerColor = Decoration::Bold,

        /**
         * Color/decoration applied to any cell whose column has no {@see \Medas\Console\ColumnDef}
         * style of its own (and which isn't an already-formatted {@see \Medas\Console\Text}).
         * Only `Style::colorFormats()` is consulted — a `Style`'s own layout is not used here,
         * since cell layout is always owned by the column.
         */
        public Style|null $defaultCellStyle = null,
    )
    {
    }
}
