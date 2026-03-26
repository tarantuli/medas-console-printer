<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables\TablePrinter;

use Medas\Console\Formats\{Format, HexColor, Style};

class Settings
{
    public function __construct(
        public Format $lineColor = new HexColor('#005f00'),
        public Format $headerColor = Style::Bold,
    )
    {
    }
}
