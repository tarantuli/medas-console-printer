<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Tables\TablePrinter;

use Medas\Console\Table;
use Medas\ConsolePrinter\{ConsolePrinter, Tables\Column};

class Job
{
    /** @var Column[] */
    public array $columns = [];

    public function __construct(
        public Table          $table,
        public ConsolePrinter $printer,
        public Settings       $settings,
    )
    {
    }
}
