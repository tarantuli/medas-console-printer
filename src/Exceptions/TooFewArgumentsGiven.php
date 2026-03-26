<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class TooFewArgumentsGiven extends BaseException
{
    public function __construct(int $argumentCount, int $minArgumentCount)
    {
        parent::__construct($argumentCount, $minArgumentCount);
    }

    public function pattern(): string
    {
        return 'Too few arguments given: %s, expected at least %s';
    }
}
