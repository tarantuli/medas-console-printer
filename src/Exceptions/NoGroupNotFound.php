<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoGroupNotFound extends BaseException
{
    public function __construct(array $groupNames)
    {
        parent::__construct($groupNames ? 'path ' . implode(', ', $groupNames) : 'no path');
    }

    public function pattern(): string
    {
        return 'Cannot find a group with %s';
    }
}
