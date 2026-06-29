<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\ConsoleCommandGroup;

class CommandNotFound extends FinderException
{
    public function __construct(string $name, ConsoleCommandGroup $parent)
    {
        parent::__construct($name, $parent->path());
    }

    public function pattern(): string
    {
        return 'Cannot find commands starting with %s in group %s';
    }
}
