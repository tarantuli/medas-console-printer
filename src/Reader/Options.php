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

        /**
         * If the input is an empty string, and there is a default value, the default is used
         */
        public mixed          $default = null,
        public bool           $doTrim = true,
    )
    {
    }
}
