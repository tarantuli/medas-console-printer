<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Reader;

use Medas\Console\Printable;

class Options
{
    public function __construct(
        public Printable|null $prompt = null,

        /**
         * The validator should accept one argument, the input, and return a boolean
         */
        public \Closure|null  $validator = null,
        public bool           $doTrim = true,
    )
    {
    }
}
