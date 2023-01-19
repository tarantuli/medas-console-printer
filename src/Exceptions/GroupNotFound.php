<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\Core\Exceptions\BaseException;

class GroupNotFound extends BaseException
{
    private bool $parentIsRoot;

    public function __construct(ConsoleCommandGroup|null $parent, string $name)
    {
        $this->parentIsRoot = $parent === null;
        parent::__construct($parent ? $parent->path() : '', $name);
    }

    public function pattern(): string
    {
        return $this->parentIsRoot
            ? 'Cannot find root%s groups starting with %s'
            : 'Cannot find groups starting with %2$s in group %1$s';
    }
}
