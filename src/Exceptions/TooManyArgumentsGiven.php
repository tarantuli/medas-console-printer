<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class TooManyArgumentsGiven extends BaseException
{
    /** @param string[] $unexpectedValues */
    public function __construct(int $argumentCount, int $maxArgumentCount, array $unexpectedValues)
    {
        parent::__construct($argumentCount, $maxArgumentCount, implode(', ', $unexpectedValues));
    }

    public function pattern(): string
    {
        return 'Too many arguments given: %s, expected at most %s (unexpected: %s)';
    }
}
