<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Printable;
use Medas\Core\Exceptions\BaseException;

class NoPrintingImplementationForBlockType extends BaseException
{
    public function __construct(Printable $block)
    {
        parent::__construct($block::class);
    }

    public function pattern(): string
    {
        return 'no printing implementation found for block type %s';
    }
}
