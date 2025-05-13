<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\{ConsoleCommand, ConsoleCommandGroup};
use Medas\Core\Exceptions\BaseException;

class NoUniqueCommandFound extends BaseException
{
    public function __construct(string $name, ConsoleCommandGroup $parent, array $matches)
    {
        $matchNames = array_map(fn(ConsoleCommand $match) => $match->name(), $matches);

        parent::__construct($name, $parent->path(), implode(', ', $matchNames));
    }

    public function pattern(): string
    {
        return 'Cannot find a unique command starting with %s in group %s, found %s';
    }
}
