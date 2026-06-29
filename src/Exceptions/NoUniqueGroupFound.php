<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\ConsoleCommandGroup;

class NoUniqueGroupFound extends FinderException
{
    public function __construct(ConsoleCommandGroup|null $parent, string $name, array $matches)
    {
        $matchNames = array_map(fn(ConsoleCommandGroup $match) => $match->name(), $matches);

        parent::__construct(
            $name,
            $parent ? $parent->path() : '[root]',
            implode(', ', $matchNames)
        );
    }

    public function pattern(): string
    {
        return 'Cannot find a unique group starting with %s in group %s, found %s';
    }
}
