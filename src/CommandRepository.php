<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{CommandRepository as ConCommandRepository, Commands\ConsoleCommand, Commands\ConsoleCommandGroup};
use Medas\Core\{Attributes\Service, Interfaces\PrimesCache};
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
class CommandRepository implements ConCommandRepository, PrimesCache
{
    private array $groups;
    private array $processors;

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    /** @return ConsoleCommandGroup[] */
    public function getGroups(ConsoleCommandGroup $parent = null): array
    {
        $groups = [];

        foreach ($this->getAllGroups() as $group) {
            if ($group->parent() === $parent) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /** @return ConsoleCommandGroup[] */
    public function getAllGroups(): array
    {
        if (!isset($this->groups)) {
            $groupNames = $this->cacheManager->get()->get([$this::class, 'getAllGroupNames'], function () {
                return $this->findAllGroupNames();
            });

            $this->groups = [];

            foreach ($groupNames as $groupName) {
                $this->groups[] = service($groupName);
            }
        }

        return $this->groups;
    }

    /** @return string[] */
    private function findAllGroupNames(): array
    {
        $groupNames = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $class = new \ReflectionClass($className);

            if (!$class->implementsInterface(ConsoleCommandGroup::class)) {
                continue;
            }

            $groupNames[] = $className;
        }

        return $groupNames;
    }

    /** @return ConsoleCommand[] */
    public function getProcessors(ConsoleCommandGroup $parent): array
    {
        $processors = [];

        foreach ($this->getAllCommands() as $processor) {
            if ($processor->group() === $parent) {
                $processors[] = $processor;
            }
        }

        return $processors;
    }

    /** @return ConsoleCommand[] */
    public function getAllCommands(): array
    {
        if (!isset($this->processors)) {
            $processorNames = $this->cacheManager->get()->get([$this::class, 'getAllProcessorNames'], function () {
                return $this->findAllProcessorNames();
            });

            $this->processors = [];

            foreach ($processorNames as $processorName) {
                $this->processors[] = service($processorName);
            }

            usort(
                $this->processors,
                fn(ConsoleCommand $a, ConsoleCommand $b) => strcasecmp($a->fullCommand(), $b->fullCommand())
            );
        }

        return $this->processors;
    }

    /** @return string[] */
    private function findAllProcessorNames(): array
    {
        $processorNames = [];

        foreach (sm()->getServiceClassNames() as $className) {
            $class = new \ReflectionClass($className);

            if ($class->isAbstract()) {
                continue;
            }

            if (!$class->implementsInterface(ConsoleCommand::class)) {
                continue;
            }

            $processorNames[] = $className;
        }

        return $processorNames;
    }

    public function primeCache(): void
    {
        $this->getAllGroups();
        $this->getAllCommands();
    }
}
