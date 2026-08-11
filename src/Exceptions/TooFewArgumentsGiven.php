<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class TooFewArgumentsGiven extends BaseException
{
    /** @param string[] $missingArguments */
    public function __construct(int $argumentCount, int $minArgumentCount, array $missingArguments)
    {
        parent::__construct($argumentCount, $minArgumentCount, implode(', ', $missingArguments));
    }

    public function pattern(): string
    {
        return 'Too few arguments given: %s, expected at least %s (missing: %s)';
    }
}
