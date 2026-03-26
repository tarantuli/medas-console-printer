<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class DisallowedOptionValueGiven extends BaseException
{
    public function __construct(string $name, string $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Option %s doesn\'t allow a value, but %s is given';
    }
}
