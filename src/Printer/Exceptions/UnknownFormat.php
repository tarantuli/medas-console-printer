<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Printer\Exceptions;

use Medas\Console\Formats\Format;
use Medas\Core\Exceptions\BaseException;

class UnknownFormat extends BaseException
{
    public function __construct(Format $format)
    {
        parent::__construct($format);
    }

    public function pattern(): string
    {
        return 'Unknown format %s';
    }
}
