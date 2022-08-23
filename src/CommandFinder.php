<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\Commands\{ConsoleCommand, ConsoleCommandGroup};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class CommandFinder
{
    public function __construct(
        private readonly CommandRepository $repository,
    )
    {
    }

    public function find(string $command): ConsoleCommand
    {
        $groupNames = explode(':', $command);
        $processorName = array_pop($groupNames);

        $group = $this->findGroup($groupNames);

        return $this->findProcessor($group, $processorName);
    }

    private function findGroup(array $groupNames): mixed
    {
        $group = null;

        foreach ($groupNames as $groupName) {
            $group = $this->findNextGroup($group, $groupName);
        }

        return $group;
    }

    private function findNextGroup(ConsoleCommandGroup|null $group, string $groupName): mixed
    {
        $children = $this->repository->getGroups($group);
        $candidateGroups = [];

        foreach ($children as $child) {
            if ($child->name() === $groupName) {
                return $child;
            }

            if (str_starts_with($child->name(), $groupName)) {
                $candidateGroups[] = $child;
            }
        }

        if (count($candidateGroups) === 0) {
            throw new \Exception('can\'t find group ' . $groupName);
        }

        if (count($candidateGroups) > 2) {
            throw new \Exception('can\'t find unique group with prefix ' . $groupName);
        }

        return $candidateGroups[0];
    }

    private function findProcessor($group, $processorName): ConsoleCommand
    {
        $processors = $this->repository->getProcessors($group);
        $candidateProcessors = [];

        foreach ($processors as $processor) {
            if ($processor->name() === $processorName) {
                return $processor;
            }

            if (str_starts_with($processor->name(), $processorName)) {
                $candidateProcessors[] = $processor;
            }
        }

        if (count($candidateProcessors) === 0) {
            throw new \Exception('can\'t find command ' . $processorName);
        }

        if (count($candidateProcessors) > 2) {
            throw new \Exception('can\'t find unique command with prefix ' . $processorName);
        }

        return $candidateProcessors[0];
    }
}
