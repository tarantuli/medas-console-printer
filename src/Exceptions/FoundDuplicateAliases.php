<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FoundDuplicateAliases extends BaseException
{
    public function __construct(string $alias, string $command1, string $command2)
    {
        parent::__construct($alias, $command1, $command2);
    }

    public function pattern(): string
    {
        return 'found two commands with the alias "%s": %s and %s';
    }
}
