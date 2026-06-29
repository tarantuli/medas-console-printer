<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\ConsoleCommandGroup;

class GroupNotFound extends FinderException
{
    public function __construct(ConsoleCommandGroup|null $parent, string $name)
    {
        parent::__construct($name, $parent ? $parent->path() : '[root]');
    }

    public function pattern(): string
    {
        return 'Cannot find groups starting with %s in group %s';
    }
}
