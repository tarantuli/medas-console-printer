<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Console\{CommandRepository, Commands\ConsoleCommand, Commands\ConsoleCommandGroup};
use Medas\Core\{Attributes\Service, Interfaces\PrimesCache};

#[Service]
class Repository implements CommandRepository, PrimesCache
{
    private array $groups;
    private array $processors;
    private array $aliases;

    public function findAlias(string $command): ConsoleCommand|null
    {
        if (!isset($this->aliases)) {
            $this->aliases = cache(
                [$this::class, 'getAllAliases'],
                fn() => $this->findAllAliases()
            );
        }

        if (!array_key_exists($command, $this->aliases)) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return service($this->aliases[$command]);
    }

    private function findAllAliases(): array
    {
        $aliases = [];

        foreach ($this->getAllCommands() as $processor) {
            foreach ($processor->aliases() as $alias) {
                if (!preg_match('/^[a-z0-9][a-z0-9.-]*[a-z0-9]$/', $alias)) {
                    throw new Exceptions\FoundInvalidAlias($alias);
                }

                if (array_key_exists($alias, $aliases)) {
                    throw new Exceptions\FoundDuplicateAliases(
                        $alias,
                        $processor::class,
                        $aliases[$alias]
                    );
                }

                $aliases[$alias] = $processor::class;
            }
        }

        return $aliases;
    }

    /** @return ConsoleCommandGroup[] */
    public function getGroups(ConsoleCommandGroup|null $parent = null): array
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
            $groupNames = cache(
                [$this::class, 'getAllGroupNames'],
                fn() => $this->findAllGroupNames()
            );

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
            $processorNames = cache(
                [$this::class, 'getAllProcessorNames'],
                fn() => $this->findAllProcessorNames()
            );

            $this->processors = [];

            foreach ($processorNames as $processorName) {
                $this->processors[] = service($processorName);
            }

            usort(
                $this->processors,
                fn(ConsoleCommand $a, ConsoleCommand $b) => strcasecmp(
                    $a->fullCommand(),
                    $b->fullCommand()
                )
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
