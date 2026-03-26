<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MultipleShortCodesWithValueGiven extends BaseException
{
    public function __construct(string $codes, string $value)
    {
        parent::__construct($codes, $value);
    }

    public function pattern(): string
    {
        return 'Multiple short codes %s given plus a value %s';
    }
}
