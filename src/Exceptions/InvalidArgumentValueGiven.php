<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidArgumentValueGiven extends BaseException
{
    public function __construct(string $name, string $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Invalid value given for argument %s: %s';
    }
}
