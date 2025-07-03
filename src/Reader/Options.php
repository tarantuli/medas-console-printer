<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Reader;

use Medas\Console\Printable;

class Options
{
    public Printable|null $prompt;

    /**
     * The validator should accept one argument, the input, and return a boolean
     */
    public \Closure|null $validator;

    public bool $doTrim = true;
}
