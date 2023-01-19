<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\Core\Exceptions\BaseException;

class NoUniqueGroupFound extends BaseException
{
    private bool $parentIsRoot;

    public function __construct(ConsoleCommandGroup|null $parent, string $name, array $matches)
    {
        $this->parentIsRoot = $parent === null;
        $matchNames = array_map(fn(ConsoleCommandGroup $match) => $match->name(), $matches);

        parent::__construct($parent ? $parent->path() : '', $name, implode(', ', $matchNames));
    }

    public function pattern(): string
    {
        return $this->parentIsRoot
            ? 'Cannot find a unique root%s group starting with %s, found %s'
            : 'Cannot find a unique group starting with %2$s in group %1$s, found %s';
    }
}
