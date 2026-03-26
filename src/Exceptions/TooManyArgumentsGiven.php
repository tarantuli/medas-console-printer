<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class TooManyArgumentsGiven extends BaseException
{
    public function __construct(int $argumentCount, int $maxArgumentCount)
    {
        parent::__construct($argumentCount, $maxArgumentCount);
    }

    public function pattern(): string
    {
        return 'Too many arguments given: %s, expected at most %s';
    }
}
