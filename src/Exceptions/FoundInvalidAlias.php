<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FoundInvalidAlias extends BaseException
{
    public function __construct(string $alias)
    {
        parent::__construct($alias);
    }

    public function pattern(): string
    {
        return 'found an invalid alias name: "%s"';
    }
}
