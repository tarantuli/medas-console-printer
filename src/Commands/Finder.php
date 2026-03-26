<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands;

use Medas\Console\Commands\{ConsoleCommand, ConsoleCommandGroup};
use Medas\ConsolePrinter\Exceptions;
use Medas\Core\Attributes\Service;

#[Service]
readonly class Finder
{
    public function __construct(
        private Repository $repository,
    )
    {
    }

    public function find(string $command): ConsoleCommand
    {
        if ($processor = $this->repository->findAlias($command)) {
            return $processor;
        }

        $groupNames = explode(':', $command);
        $processorName = array_pop($groupNames);
        $group = $this->findGroup($groupNames);

        return $this->findProcessor($group, $processorName);
    }

    private function findGroup(array $groupNames): ConsoleCommandGroup|null
    {
        $group = null;

        foreach ($groupNames as $groupName) {
            $group = $this->findNextGroup($group, $groupName);
        }

        return $group;
    }

    private function findNextGroup(ConsoleCommandGroup|null $parent, string $name): ConsoleCommandGroup
    {
        $children = $this->repository->getGroups($parent);
        $candidateGroups = [];

        foreach ($children as $child) {
            if ($child->name() === $name) {
                return $child;
            }

            if (str_starts_with($child->name(), $name)) {
                $candidateGroups[] = $child;
            }
        }

        if (count($candidateGroups) === 0) {
            throw new Exceptions\GroupNotFound($parent, $name);
        }

        if (count($candidateGroups) >= 2) {
            throw new Exceptions\NoUniqueGroupFound($parent, $name, $candidateGroups);
        }

        return $candidateGroups[0];
    }

    private function findProcessor($group, $name): ConsoleCommand
    {
        $processors = $this->repository->getProcessors($group);
        $candidateProcessors = [];

        foreach ($processors as $processor) {
            if ($processor->name() === $name) {
                return $processor;
            }

            if (str_starts_with($processor->name(), $name)) {
                $candidateProcessors[] = $processor;
            }
        }

        if (count($candidateProcessors) === 0) {
            throw new Exceptions\CommandNotFound($name, $group);
        }

        if (count($candidateProcessors) >= 2) {
            throw new Exceptions\NoUniqueCommandFound($name, $group, $candidateProcessors);
        }

        return $candidateProcessors[0];
    }
}
